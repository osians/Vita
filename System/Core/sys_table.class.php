<?php

namespace Framework\Vita\Core;

/**
 *
 * Classe usada com intuito de
 * instanciar uma tabela do Banco de dados em formado
 * de propriedade para ser associada a classe
 * principal do sistema.
 * Dessa forma, pode-se manipular uma tabela do banco de dados
 * através de $vita->NomeDaTabela->metodo(...);
 *
 * Uma das principais vantagens de utilizar esta
 * metodologia, e' a possibilidade de incluir
 * filtros ou Regras de Insercao para cada campo.
 */
class SYS_Table
{
	/**
	 *    Nome da tabela
	 *    @var string
	 */
	private $tablename = "";

	/**
	 *    Guarda atributos desta tabela e regras de validacao de dados
	 *    @var array
	 */
	private $roles = array();

	/**
	 *    Indica o nome do campo que atua como chave primaria na tabela
	 *    @var string
	 */
	private $primaryKey = "";

	public function __construct($tablename = null, $attr = array())
	{
		$this->tablename = $tablename;

		$this->roles = isset($attr['roles'])
		     ? $attr['roles'] : array();

		$this->primaryKey = isset($attr['primary_key'])
		     ? $attr['primary_key'] : null;
	}

	public function setPrimaryKey($column_name) {
		$this->primaryKey = $column_name;
	}

	public function getPrimaryKey(){
		return $this->primaryKey;
	}


	/**
	 *    Salva informações na tabela. Sendo esta ação, um INSERT ou
	 *    um UPDATE dependentendo do segundo parametro recebido.
	 *
	 *    @param  mixed - (array|object) dados a serem gravados
	 *    @param  int   - caso um ID numerico seja passado, 
	 *                    faz um UPDATE do contrario, realizar 
	 *                    um INSERT
	 *
	 *    @return int   - id do registro
	 */
	public function save($dados = array(), $id = null)
	{
		if(is_object($dados)) {
			$dados = get_object_vars($dados);
		}

		if (!is_array($dados)) {
			return false;
		}

		# obtendo as propriedades desta tabela direto do MySQL ...
		$campos = $this->getFields();
		# possibilidades de uso
        # campos['campo']->field ;   // idmovimento
        # campos['campo']->type ;    // int(10) unsigned
        # campos['campo']->null ;    // NO
        # campos['campo']->key ;     // PRI
        # campos['campo']->default ; // 1
        # campos['campo']->extra ;   // auto_increment

		$sql = "";
		$_keys = array_keys($dados);
		$_vals = array_values($dados);

		# verificando indices/nomes dos campos
		# garantindn que temos apenas texto sem espaco
		for ($i=0; $i < count($_keys); $i++) {
			$_keys[$i] = vita()->validate->textns($_keys[$i]);
		}

		# prevalidacao de campos
		for($i = 0; $i < count($_vals); $i++):
			# verificando se a informacao esta de acordo com
			# o que pode ser inserido no banco de dados.
			# Pegando o campo/coluna que e suas propriedades direto
			# da tabela mysql
			$campo = $campos[$_keys[$i]];

			$__ftype = (strpos($campo->type,"("))?substr($campo->type,0,strpos($campo->type,"(")):$campo->type;
			$__ftype = strtolower($__ftype);

			# (nota) : esta funcao nao trata todos os tipos de dados oferecidos pelo mysql
			switch($__ftype)
			{
				case 'int':
				case 'tinyint':
					$_vals[$i] = vita()->validate->number( $_vals[$i] );
				break;

				case 'decimal':
					$_vals[$i] = vita()->validate->dcmm($_vals[$i]);
				break;

				case 'date':
					$_vals[$i] = vita()->validate->mysqlDate($_vals[$i]);
				break;

				case 'datetime':
					$_vals[$i] = vita()->validate->mysqlDate($_vals[$i]);
				break;

				default:
					$_vals[$i] = $_vals[$i] ;
				break;
			}

		endfor;

		# daqui em diante, sabemos que os campos recebidos,
		# sao candidados validados a entrar na base de dados.
		# agora, checamos apenas se ha filtros para definir de que maneira
		# esses dados devem ser registrados. por exemplo, um campo do tipo TEXT
		# pode ser definido pelo Programador para entrar apenas letras de A a F
		# e sem espaços.
		if(!is_null($id)){ // update
			$sql = "UPDATE `".vita()->config->dbname."`.`".$this->tablename."` SET ";

			# percorrendo os dados e realizando filtros
			$__lastitem = count( $dados ) - 1;
			for($i = 0; $i < count( $dados ); $i++):
				if(isset($this->roles[$_keys[$i]])):
					$role = $this->roles[$_keys[$i]];
					$_vals[$i] = vita()->validate->$role( $_vals[$i] );
				else:
					$_vals[$i] = vita()->validate->make_safe( $_vals[$i] );
				endif;
				$sql .= " `".$_keys[$i]."` = '". $_vals[$i] ."'" ;
				$sql .= $__lastitem == $i ? " " : ", ";
			endfor;

			# gerando a query final
			$sql .= "WHERE `".$this->primaryKey."` = $id ;";

			# executando query
        	vita()->db->query( $sql );
        	vita()->db->execute();
        	return $id;
		}
		else{ // insert

			# verificando valores
			for($i = 0; $i < count($_vals); $i++):

				# verifica se foi definido um filtro para este campo
				# para padronizar o seu registro no banco de dados.
				if(isset($this->roles[$_keys[$i]])):
					$role = $this->roles[$_keys[$i]];
					$_vals[$i] = vita()->validate->$role( $_vals[$i] );
				else:
					$_vals[$i] = vita()->validate->make_safe( $_vals[$i] );
				endif;

			endfor;

			# gerando a query final
			$sql = "INSERT INTO `".vita()->config->dbname."`.`".$this->tablename."`";
			$sql .= "(`".implode("`, `",$_keys)."`) VALUES ";
			$sql .= "('".implode("', '",$_vals)."');";

			# executando query
        	vita()->db->query( $sql );
        	vita()->db->execute();
        	return vita()->db->lastInsertId();
		}
	}


	/**
	 *    Verifica se a tabela alvo desta classe
	 *    existe no banco de dados.
	 *
	 *    @return bool - true caso exista
	 */
	public function exists()
	{
		$sql = "SHOW TABLES LIKE '$this->tablename';" ;
       	vita()->db->query($sql);
       	return (vita()->db->rowCount() > 0);
	}

    public function select($colunas = '*')
    {
        $sql = "SELECT $colunas FROM `"
             . vita()->config->dbname
             . "`.`$this->tablename` ";

        return new QSet($sql);
    }

    public function getFields()
    {
    	$attrs = vita()->db->select(
    		"DESC `".vita()->config->dbname."`.`{$this->tablename}`;"
    	);

    	$properties = null;
    	foreach ($attrs as $key => $value) {
    		$properties[$value->field] = $value;
    		if($value->key == 'PRI') {
    			$this->setPrimaryKey($value->field);
    		}
    	}
    	return $properties;
    }

    public function del($id = 0) {

    	if (!isset($this->primaryKey)) {
    		$campos = $this->getFields();
    	}

    	$database = vita()->config->dbname;
    	$sql = "DELETE 
    	        FROM `{$database}`.`{$this->tablename}` 
    		    WHERE `{$this->tablename}`.`{$this->primaryKey}` = :fid;";

    	vita()->db->query($sql);
    	vita()->db->bind(':fid', $id);
    	return vita()->db->execute();
    }

}


class QSet
{
	/**
	 *    Query a ser executada na base
	 *    @var String
	 */
    private $sql;

    private $count;

    public function __construct($sql = '') {
        $this->sql = $sql ;
    }

    /**
     *    Retorna apenas o primeiro resultado
     *    @return  Object
     */
    public function single(){
    	return $this->result(true);
    }

    /**
     *    Retorna todos os resultados encontrados
     *    @param boolean - se True, apresenta apenas o primeiro registro
     *    @return array of Objects
     */
    public function result($firstOnly = false) {
		return ($firstOnly)
			? vita()->db->selectFirst("{$this->sql};")
			: vita()->db->select("{$this->sql};");
    }


    public function where($condicoes = '')
	{
		if (empty($condicoes)) {
			$condicoes = '1';
		}

		if( is_string($condicoes) )
            $this->sql .= "WHERE $condicoes";

		if( is_array($condicoes)):
			$this->sql .= "WHERE 1";
			foreach($condicoes as $k => $v):
				$this->sql .= " AND `$k` = '$v'" ;
			endforeach;
		endif;

        return new self($this->sql);
    }


    /**
     *    JOIN
     *    @param  String $tname - Nome da Tabela
     *    @param  String $jtype - Tipo de join (INNER|LEFT|etc)
     *    @param  String $on - condicao do Join 
     *            (ex: categorias.id = parent.idcategoria)
     *    @return Object - QSet
     */
	private function join($tname = null, $jtype = null, $on = null)
	{
		# caso vazio apenas retorna uma nova instancia deste objeto
		if (is_null($tname) || empty(trim($tname))) {
			return new self($this->sql);
		}
		$on = (null == $on) ? "" : " ON ".trim($on);
		$this->sql .= " {$jtype} JOIN {$tname} {$on} ";
        return new self($this->sql);
	}


	/**
	* Realiza um Inner Join adicionando uma outra tabela
	*
	* @param  string $tablename - nome da tabela a adicionar
	* @param  string|Array $on - condicao que a tabela deve obedecer para adicao
	* @return Qset
	*/
	public function innerJoin($tablename, $on = null) {
		return $this->join($tablename, 'INNER', $on);
	}

	/**
	* Realiza um Left Join adicionando uma outra tabela
	*
	* @param  string $tablename - nome da tabela a adicionar
	* @param  string|Array $on - condicao que a tabela deve obedecer para adicao
	* @return Qset
	*/
	public function leftJoin($tablename, $on = null) {
		return $this->join($tablename, 'LEFT', $on);
	}

	/**
	* Realiza um Right Join adicionando uma outra tabela
	*
	* @param  string $tablename - nome da tabela a adicionar
	* @param  string|Array $on - condicao que a tabela deve obedecer para adicao
	* @return Qset
	*/
	public function rightJoin($tablename, $on = null) {
		return $this->join($tablename, 'RIGHT', $on);
	}

	/**
	 * Ha casos em que o ON precisa vir apos um novo INNER, ex:
	 * LEFT JOIN categorias
	 *     INNER JOIN categorias cat ON cat.idparent = categorias.idcategoria
	 * ON categorias.idcategoria = tabela.idcategoria...
	 *
	 * @param  [type] $on [description]
	 * @return [type]     [description]
	 */
	public function on($on = null){
		$this->sql .= " ON $on ";
        return new self($this->sql);
	}

	/**
	 * Retorna a SQL criada dentro do objeto QSet
	 * @return string
	 */
    public function sql(){
        return $this->sql;
    }


    # ---------- acima revisado e ok -------------

    public function exec($firstOnly = false)
	{
		# checa se banco de dados foi instanciado...
		# @todo - implementar verificacao e busca no SQLite tambem!
		if (!isset(vita()->db)) {
			show_error( 
				'Banco de dados MySQL não foi instanciado no sistema.
				 Corrija o erro, verificando se no arquivo config.php o
				 indice $config[load_mysql] está setado como TRUE.' );
		}

		$qt = strtolower(substr($this->sql, 0, strpos($this->sql, ' ')));
		switch($qt)
		{
			case 'select':
				return ($firstOnly) 
				     ? vita()->db->select_first($this->sql .";") 
				     : vita()->db->select( $this->sql .";" );
				break;
			case 'update':
				return vita()->db->update($this->sql);
				break;
			default:
				show_error("Consulta não identificada ". $this->sql);
				break;
		}
    }

    /* provavelmente sera Obsoleta nesta nova classe Table */
    public function old_from( $__tabela__ = null )
    {
        if(is_null($__tabela__))
            show_error( "Nenhuma tabela candidata para criação da consulta SQL." );

        $this->sql.= "FROM {$__tabela__} ";
        return new self($this->sql);
    }

    /**
     * Insere um limite na numerico na consulta SQL , ex: Pegar apenas 20 registros
     * ou pegar a partir do registro 20, 10 registros
     *
     * @param  int $param1 - Limte, caso seja passado tambem o $param2, $param1 passa a ser inicio, e $param2 = Quantidade
     * @param  int $param2 - Qtd, caso passado torna-se quantidade a ser obtida de registros
     * @return new Self
     */
    public function limit( $param1 = null, $param2 = null)
    {
		if( !is_null($param2) && is_numeric($param2))
			$this->sql .= " LIMIT $param1, $param2";
		else
			$this->sql .= " LIMIT $param1";

		return new self( $this->sql );
    }

	/**
	 * Metodo Set usado para casos de update
	 * ex: Query::update( 'tabela' )->set( array(...) )
	 *
	 * @param string|array
	 * @return new Self
	 **/
	public function set( $setdata )
	{
		if(empty($setdata)) return false ;

		if( is_string($setdata) ):
            $this->sql .= " SET $setdata";
		endif;

		if( is_array($setdata)):
			$this->sql .= " SET";
			foreach($setdata as $k => $v):
				$this->sql .= " `$k` = '$v'," ;
			endforeach;
			$this->sql = trim($this->sql,',');
			return new self($this->sql." ");
		endif;

        return new self($this->sql);
	}

	/**
	 *    Metodo usado unicamente em uma consulta 
	 *    SELECT para realizar paginacao no sistema
	 *    @return int - total de registros na consulta atual
	 */
	public function count() {
		vita()->db->select($this->sql .";");
		return vita()->db->rowCount();
	}

}