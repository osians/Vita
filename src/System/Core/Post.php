<?php

namespace Vita\System\Core;

use \Vita\System\Core\Config\SysVitaConfigVisibilidadeEnum;

/**
 *
 * Classe simples, responsavel por tratar dados advindos de formularios
 * @todo - implementar novos mecanismos de filtro e segurancao de dados
 * @todo - implementar mecanismo para permitir posts com tags html
 *
 * @dependencias - SYS, SYS_Table, SYS_Validate
 **/
class Post
{
	// @var array - guarda dados de post
	private $_post;

	// @var bool - se true, nao elimita post original $_POST
	private $manter_post = false;

	// indica se deve setar as propriedades deste objeto como Privada ou Publica
	private $mode = sysVitaConfigVisibilidadeEnum::PRIVADA;

	public function __construct( $__manter_post = false ){
		$this->manter_post = $__manter_post;
	}

	/**
	 * Recebe o $_POST do sistema, e trata-o antes que possa ser
	 * utilizado no sistema
	 **/
    public function init()
	{
		if(!$_POST) return;
		$_post = $_POST;

		foreach($_post as $k => $v):
			$this->set($k,$v);
		endforeach;

		if(isset($_FILES)):
			foreach ($_FILES as $key => $value):
				if(empty($value["name"])) continue;
				if($this->mode == sysVitaConfigVisibilidadeEnum::PRIVADA)
					$this->_post[$key] = $value;
				else
					$this->$key = $value;
			endforeach;
		endif;

		# reseta post para forcar no sistema uso
		# desta class para manipulacao dos dados no form
		if($this->manter_post == false)
        	unset($_POST);

        # setando outras infos importantes ao form
        $this->set('received',true );
        $this->set('action', ''); //&$_GET['request']
        $this->set('method', $_SERVER['REQUEST_METHOD']);

        # HTTP_REFERE nao e' garantido ser enviado pelo cliente
        if(isset($_SERVER['HTTP_REFERER'])){
        	$this->set('caller', $_SERVER['HTTP_REFERER']);
        }
    }

    protected function make_safe( $value )
    {
    	if(is_array($value)):
    		$_retorno = array();
    		foreach ($value as $key => $val):
    			$_retorno[$key] = vita()->validate->mres( trim($val) ) ;
    			#$_retorno[$key] = htmlspecialchars(vita()->validate->mres(trim($val)),ENT_QUOTES,'UTF-8');
    		endforeach;
    		return $_retorno;
    	else:
       		return vita()->validate->mres( trim($value) ) ;
		endif;
    }

    /**
     * Autoprocessa formularios
     * @return
     */
    public function autoprocess()
    {
    	# temos um post capiturado pelo sistema?
		if( $this->received == true ):

			$_validate_message = array();

			# verficando se nossa palavra magica esta setada
			# caso sim, indica que este script deve continuar
			# e processar automaticamente os dados capiturados.
			$tablename = $this->formname ;

			# se este form, nao tem comando autoprocessar, retorne.
			if(null === $tablename) return;

			# formname nao sera mais util no sistema
			$this->del( 'formname' ) ;

			# se a tabela nao foi definida no arquivo de config,
			# e nem iniciada via codigo, tenta instanciala aqui.
			if(null === vita()->$tablename)
			{
				vita()->$tablename = new SYS_Table( $tablename );
				# checando se a tabela existe de verdade
				if(!vita()->$tablename->exists()) throw new SysException("
					Parece que a tabela '$tablename' na qual você esta tentando aplicar o autoProcessamento,
					não existe na base de dados.");
			}

			# nome da chave primaria desta tabela
			$pk = vita()->$tablename->getPrimaryKey();

			# verificando se e' uma atualizacao ou um insert
			$__id = ($pk != null) ? $this->$pk : 0;
			$_dados = array();

    		# dados a serem evitados nos forms
			$_avoid = array('formname','received','action','method','caller','smessage', 'emessage');

			if( $__id ): // update
				$this->del( $pk );
			else: // insert
				$this->$pk = 0;
				$__id = null;
			endif;

			# percorrendo cada um dos campos que foram postados
			# no form, validando, filtrando e verificando se esta
			# de acordo com o desejado.
			foreach ($this->_post as $__key => $__value):

				// por padrao esta classe guarda algumas keys e values
				// a mais, para possiveis necessidades. aqui, tentamos
				// evitar essas informacoes e obter apenas o que interessa
				// para o banco de dados.
				if( in_array($__key, $_avoid) ) continue;

				// caso a chave seja vazia, indica um provvel erro vindo
				// do frontend, tentamos evitar tambem.
				if(empty($__key)) continue;

				// checando se ha comandos extras no Key do form exemplo 'name="senha|compare"'
				if( !(strpos( $__key, "|" ) === false) ):
					$_tmp = explode("|",$__key);
					if(isset($_tmp[1])):
						$__tmp = $__key;
						$__key = null;
						foreach ($_tmp as $val):
							if( null == $__key ):
								$__key = trim($val);
							else:
								$__cmd = trim($val);
								// ignore apenas indica que o campo nao deve ser
								// passado ao banco de dados.
								if($__cmd == 'ignore') continue;

								// compare e' usado para verificar se 2 campos sao iguais
								if($__cmd == 'compare'){
									$compare = trim($_tmp[0]);
									if( $_dados[$compare] != $__value ){
										# dados de comparacao incorretos... matar processo
										$_validate_message[] = "Há campos na tabela que precisam ser semelhantes." ;
									}
									continue;
								}else{
									if(function_exists($__cmd)){
										$__value = call_user_func_array($__cmd, array(&$__value));
									}
								}
							endif;
						endforeach;
					endif;
				endif;
				// a informacao esta apta a ser remetida ao banco
				$_dados[$__key] = $__value ;

			endforeach;

			if(sizeof($_validate_message) == 0){
				// registrando dados e retorna o id, se tudo ok
				$r = vita()->$tablename->save( $_dados, $__id );
				// successo guarda o id gerado durante o insert na tabela
				$this->success = $r;
			}else{
				$this->success = 0;
				$this->error_message = implode(", ", $_validate_message);
			}

		endif;
    }

	/**
	 * Obtem um valor postado num formulario, dado o seu Name|Key
	 * uso : Post::get( 'cnpj' );
	 *
	 * @param string - Name
	 * @return mixed | null(caso nao encontrado)
	 **/
	public function get( $__key ){
		return (isset($this->_post[$__key])) ? $this->_post[$__key] : null;
	}

    /**
     * Torna publica as propriedades deste objeto
     * que podem ser acessadas via twig template.
     * Este metodo é executado automaticamente, no final
     * de todos os processos, quando o sistema for
     * compilar o template final.
     *
     * {{ vita.post.propriedade }}
     *
     * @return array()
     */
	public function publicar(){
		$this->mode = sysVitaConfigVisibilidadeEnum::PUBLICA;
		if(count($this->_post) > 0)
			foreach ($this->_post as $key => $value)
				$this->set($key,$value);
	}

	/**
	 * Seta uma nova informacao no formulario
	 * @todo - criar melhor mecanismo de validacao e filtro de dados
	 **/
	public function set( $__key, $__value){
		if(empty($__key)) return;
		$k = $this->make_safe( $__key );
		$v = $this->make_safe( $__value );
		if($this->mode == sysVitaConfigVisibilidadeEnum::PRIVADA)
			$this->_post[$k] = $v;
		else
			$this->$k = $v;
	}

	public function del( $__key ){
		if(isset($this->_post[$__key]))
			unset($this->_post[$__key]);
	}

    public function __get($__key){
        return $this->get( $__key );
    }

    public function __set($__key,$__value){
        $this->set( $__key, $__value );
    }

	public function dump(){
		return $this->_post;
	}
}