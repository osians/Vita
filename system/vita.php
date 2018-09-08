<?php

namespace Framework\Vita;

/* ---------------------------
 -- Vita brevis,
 -- ars longa,
 -- occasio praeceps,
 -- experimentum periculosum,
 -- iudicium difficile.
                 (Hipócrates)
 --------------------------- */

# (nota) a opcao de __autoload() foi removida, para permitir que
# sistemas que implementem o vita como base, possam implantar suas
# proprias rotinas de autoload

require_once 'bootstrap.php';

use \Framework\Vita\Core\DBFactory ;
use \Framework\Vita\Core\SysException;
use \Framework\Vita\Core\SYS_Table;
use \Exception;

final class Vita
{
	// Vita::version;
	const version = '20170519-142902';

	/**
	* Responsavel por gravar informacoes em
	* arquivo de log
	*
	* @var object SYS_Log
	*/
    public $log = null;

	/**
	* Todas as configuracoes do sistema,
	* sejam elas em arquivos ou Banco de dados
	* ficam acessiveis, atraves deste objeto
	*
	* @var object SYS_Config
	*/
    public $config = null;

    // @var object SYS_Session
    public $session = null;

    // @var object SYS_Validate
    public $validate = null;

    // @var object SYS_Upload
    public $upload = null;

    /**
     * Driver que faz o gerenciamento PDO
     * de conexao a diferentes fontes de dados
     *
     * @var object SYS_Db
     */
    public $db = null;
    public $database = null ;

    // @var object SYS_Db
    public $sqlite = null;

    // @var object SYS_Utils
    public $utils = null;

    /**
     * Quando um Formulário emite um POST, este
     * Objeto trata o Post, limpa os dados de
     * caracteres indesejados e torna as informações
     * acessiveis para o sistema.
     *
     * @var object SYS_Post
     */
    public $post = null;

	/**
	 * Armazena variaveis temporarias que ficaram
	 * disponiveis para todo o sistema, enquanto o
	 * mesmo estiver em execução.
	 * Valores são acessados atraves dos metodos magicos
	 * __set e __get
     * 
	 * @var array
	 **/
	private $vars = array();

    // @var object self
	private static $instance;

	/**
	 * Objeto Twig. Usado para gerenciamento do sistema
	 * de Tags. Permite maior flexibilidade ao trabalhar 
     * no frontend.
     *
     * @var object
	 */
	private $twig = null;

	/**
	 * Objeto Mail. Representa a instancia do PHPMailer
	 * 
	 * @var object - PHPMailer
	 **/
	public $mail = null;
	
    private function __construct(){}
	private function __clone(){}
	private function __wakeup(){}

	public function init()
	{			
		// obtendo array do arquivo config.php
        GLOBAL $_config;

    	$this->config   = new \Framework\Vita\Core\SYS_Config( $_config );
        $this->log      = new \Framework\Vita\Core\SYS_Log( $this->config->log_folder );
        $this->session  = new \Framework\Vita\Core\SYS_Session( $this->config->session_expire_time );
        $this->validate = new \Framework\Vita\Core\SYS_Validate();
        $this->utils    = new \Framework\Vita\Core\SYS_Utils();

        // tratamento de $_POST para formularios
        $this->post     = new \Framework\Vita\Core\SYS_Post( false );
        $this->post->init();

        // tratamento de upload de arquivos
        $this->upload    = new \Framework\Vita\Core\SYS_Upload();
        $_upload_config_ = array
        (
		    'destination'    => $this->config->upload_folder,
		    'overrideFile'   => FALSE,
		    'randomFileName' => FALSE,
		    'maxsize'        => $this->config->max_file_size,
		    'max_imgWidth'   => $this->config->max_img_width,
		    'max_imgHeight'  => $this->config->max_img_height,
		    'printErrors'    => FALSE,
        );
        $this->upload->init( $_upload_config_ );

		date_default_timezone_set( $this->config->default_time_zone );

		# verificando se deve instanciar mysql ...
		if($this->config->load_mysql):
	        // setando conexao com mysql
	        $_conexao_dados_ = array(
	            'host'  => $this->config->dbhost,
	            'port'  => $this->config->dbport,
	            'user'  => $this->config->dbuser,
	            'pass'  => $this->config->dbpass,
	            'dbname'=> $this->config->dbname
	        );
	        $this->db = DBFactory::create( 'MySQL', $_conexao_dados_ );
	        $this->database = &$this->db;
        endif;

		# caso queira criar um database SQLite, descomentara a funcao abaixo
		if($this->config->load_sqlite):
	        # setando conexao com sqlite
	        $_conexao_dados_sqlite_ = array(
	            'dbpath' => $this->config->sqlite_folder,
	            'dbname' => $this->config->sqlite_dbname
	        );
        	$this->sqlite = DBFactory::create( 'SQLite', $_conexao_dados_sqlite_ );
        endif;

		# carregando objeto de envio de e-mail
		$this->mail = new \PHPMailer;
		
        # verificando por tabelas do banco de dados a serem agregadas ao sistema
        if(isset($_config['SYS_Table']) && is_array($_config['SYS_Table']) )
        	foreach ($_config['SYS_Table'] as $tablename => $_attrs )
        		$this->loadTable( $tablename, $_attrs );

        # verificando se ha formularios para autoprocessamento
        $this->post->autoprocess();

        # @todo - carrega librarias externas definidas pelo usuario
	}

	/**
	* Essa funcao, recebe como parametro o nome de um arquivo 
    * do Frontend para exibicao.
    * Quando o arquivo for chamado, as variaveis processadas no 
    * sistema serao todas passadas a essa view. Dessa forma 
    * a view podera usar as variaveis atraves do sistema de Tags TWIG.
    * 
	* @return [type] [description]
	*/
	public function compile( $__viewName = null ){
        try
        {
            if(!strpos($__viewName, '.twig')) $__viewName .= '.twig';

            $__view_folder = $this->config->app_folder . $this->config->view_folder . DIRECTORY_SEPARATOR;
            $view = $__view_folder . $__viewName;

            if(!file_exists($view)):
                throw new Exception( 'O arquivo "'.$__viewName.'" não foi encontrado em "'.$__view_folder.'". Proceda com a criação do mesmo para corrigir o problema.' );
            endif;

			# obtem todas os parametros de configuracoes
			$_tmp = get_object_vars($this->config);
			$_vars = array();
			$_vars = $this->getVars();

			# verificando e chamando metodo que torna
			# publicas as propriedades dos objetos
			foreach(get_object_vars(Vita::getInstance()) as $o)
				if( method_exists($o,'publicar'))
					call_user_func(array($o, 'publicar'));

			$_vars['vita'] = $this;
            
			print $this->twig->render( $__viewName, $_vars );
        }
        catch( Exception $e )
        {
        	throw new SysException( $e->getMessage() );
        	#$this->warning( $e->getMessage(), "Erro 404" );
            exit(0);
        }
        exit(0);
	}

	public function loadTable( $tablename, $attrs = null ){
   		$this->$tablename = new SYS_Table( $tablename, $attrs );
	}

	/**
	 * Se uma variavel foi definida nesta classe, trata-a de forma global
	 * retorna qualquer variavel setada na classe.
	 *
	 * @param  string $name - nome identificador da variavel
	 * @return Mixed
	 */
    public function __get($name){
		return $this->get($name);
    }

    public function get($name){
    	return isset($this->vars[trim($name)])?$this->vars[trim($name)]:null;
    }

    public function getVars(){
    	return $this->vars;
    }

    /**
     * Seta uma variavel na classe Sys tornando a global ao resto do sistema
     * @param string $name - nome identificados da variavel
     * @param Mixed $value - valor a ser guardado
     */
    public function set($name,$value){
    	# so seta uma propriedade se a mesma nao for instancia de uma tabela,
    	# para evitar problemas.
    	if( isset( $this->vars[trim($name)] ) && $this->vars[trim($name)] instanceof SYS_Table )
    		return ;
    	# seta propriedade no objeto
        $this->vars[trim($name)] = $value;
    }

    public function __set($name,$value){
    	$this->set($name,$value);
    }

    /**
    * Inicializa o sistema Twig para gerenciamento de Template
    * 
    * @param  string $__path - caminho onde as Views (*.twig) se encontram
    */
    public function init_tpl_system( $__path ){
		# gerencia tpl
		if(!is_dir( $__path ))
			throw new SysException( "Tentativa de Iniciar o sistema gerenciador de templates em uma pasta Inexistente: '{$__path}'");

        require_once 'libraries/Twig/Autoloader.php';
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem( $__path );

        # verificando se o modo de cache esta liberado no arquivo de config
        $__cache_folder = ($this->config->twig_cache_enable === true) ? $this->config->system_path . 'cache' . DIRECTORY_SEPARATOR : false;

        # instanciando o ambiente twig
        $this->twig = new \Twig_Environment($loader, 
            array(
                'cache' => $__cache_folder, 
                'debug' => $this->config->twig_debug_enable
            )
        );

        # adiciona extensao para realizar debug
        if($this->config->twig_debug_enable)
            $this->twig->addExtension(new \Twig_Extension_Debug());

        # adicionando nosso filtro proprio dde traducoes
        $this->twig->addFilter('vtrans', new \Twig_Filter_Function('vita_twig_translate_filter'));
    }

	public static function getInstance()
    {
        if( null === static::$instance )
            static::$instance = new static();
        return static::$instance;
    }
}


