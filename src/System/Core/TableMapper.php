<?php

namespace Vita\System\Core;

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
class TableMapper
{
    /**
     *    Nome do Banco de dados usado
     *    @var string
     */
    protected $database = "";
    
    /**
     *    Nome da tabela
     *    @var string
     */
    protected $tablename = "";

    /**
     *    Guarda atributos desta tabela e regras de validacao de dados
     *    @var array
     */
    protected $roles = array();

    /**
     *    Indica o nome do campo que atua como chave primaria na tabela
     *    @var string
     */
    private $primaryKey = "";

    /**
     *    Query a ser executada na base
     *    @var String
     */
    private $sql;

    /**
     *    Total de Registros encontrados
     *    @var integer
     */
    private $count;
    
    /**
     *    Metodo construtor
     *    @param string $tablename - Nome da Tabela a Inicializar
     *    @param array $attr - atributos
     */
    public function __construct($tablename = null, $attr = array())
    {
        $this->database = vita()->config->dbname;
        $this->tablename = $tablename;
        $this->roles = isset($attr['roles']) ? $attr['roles'] : array();
        $this->primaryKey = isset($attr['primary_key']) ? $attr['primary_key'] : null;
    }

    /**
     *    Informa o nome da Coluna que e' a chave primaria da Tabela
     *    @param type $columnName
     */
    public function setPrimaryKey($columnName) {
        $this->primaryKey = $columnName;
        return $this;
    }

    /**
     *    Retorna nome da Coluna que e' a Primary Key
     *    @return string
     */
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
        if (is_object($dados)) {
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
        $sql = "SHOW TABLES LIKE '{$this->tablename}';" ;
        vita()->db->query($sql);
        return vita()->db->rowCount() > 0;
    }

    /**
     *    Realiza um Select na Tabela Atual
     *    @param string $colunas
     *    @return TableMapper
     */
    public function select($colunas = '*')
    {
        $sql = "SELECT {$colunas} FROM `{$this->database}`.`{$this->tablename}` ";
        return $this;
    }

    /**
     *    JOIN
     *    @param  String $table - Nome da Tabela
     *    @param  String $type - Tipo de join (INNER|LEFT|etc)
     *    @param  String $on - condicao do Join 
     *            (ex: categorias.id = parent.idcategoria)
     *    @return Object TableMapper
     */
    private function join($table = null, $type = null, $on = null)
    {
        if (is_null($table) || empty(trim($table))) {
            return $this;
        }
        
        $on = (null == $on) ? "" : " ON " . trim($on);
        $this->sql .= " {$type} JOIN {$table} {$on} ";

        return $this;
    }

    /**
     *    Realiza um Inner Join adicionando uma outra tabela
     *
     *    @param  string $table - nome da tabela a adicionar
     *    @param  string|Array $on - condicao que a tabela deve obedecer para adicao
     *    @return TableMapper
     */
    public function innerJoin($table, $on = null) {
        return $this->join($table, 'INNER', $on);
    }

    /**
     *    Realiza um Left Join adicionando uma outra tabela
     *
     *    @param  string $table - nome da tabela a adicionar
     *    @param  string|Array $on - condicao que a tabela deve obedecer para adicao
     *    @return Qset
     */
    public function leftJoin($table, $on = null) {
        return $this->join($table, 'LEFT', $on);
    }

    /**
     *    Realiza um Right Join adicionando uma outra tabela
     *
     *    @param  string $table - nome da tabela a adicionar
     *    @param  string|Array $on - condicao que a tabela deve obedecer para adicao
     *    @return Qset
     */
    public function rightJoin($table, $on = null) {
        return $this->join($table, 'RIGHT', $on);
    }

    /**
     *    Ha casos em que o ON precisa vir apos um novo INNER, ex:
     *    LEFT JOIN categorias
     *       INNER JOIN categorias cat ON cat.idparent = categorias.idcategoria
     *    ON categorias.idcategoria = tabela.idcategoria...
     *
     *    @param  string $on
     *    @return TableMapper
     */
    public function on($on = null) {
        $this->sql .= " ON {$on} ";
        return $this;
    }
        
    /**
     *    Adiciona condicoes Where a Query
     *    @param string $condicoes
     *    @return TableMapper
     */
    public function where($condicoes = '')
    {
        if (empty($condicoes)) {
            return $this;
        }

        if (is_string($condicoes)) {
            $this->sql .= "WHERE {$condicoes}";
            return $this;
        }

        if (is_array($condicoes)) {
            $this->sql .= "WHERE 1";
            foreach($condicoes as $k => $v) {
                $this->sql .= " AND `{$k}` = '{$v}'" ;
            }
        }

        return $this;
    }
 
    /**
     *    Insere um limite numerico na consulta SQL , ex: Pegar apenas 20 registros
     *    ou pegar a partir do registro 20, 10 registros
     *    @param  int $limit - Limit a ser obtido
     *    @param  int $offset - Offset de onde começar
     *    @return TableMapper
     */
    public function limit($limit = null, $offset = null)
    {   
        if (!is_null($limit) && is_numeric($limit)) {
            $this->sql .= " LIMIT {$limit} ";
        }
        
        if(!is_null($offset) && is_numeric($offset)) {
            $this->sql .= " OFFSET {$offset} ";
        }
        
        return $this;
    }
    
    /**
     *    Retorna apenas o primeiro resultado
     *    @return  Object
     */
    public function single() {
    	return $this->result(true);
    }

    /**
     *    Retorna um registro desta tabela, baseada no ID do mesmo
     *    @param type $id
     *    @return type
     */
    public function getById($id = 0) {
        return $this->select()
                    ->where("id_{$this->tablename} = '{$id}'")
                    ->single();
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

    /**
     *    Recebe o resultado da Consultar e faz um Parse para transformar
     *    o resultados em Models
     *    @param type $resultSet
     *    @throws Exception
     *    @return Object TableModel
     */
    protected function parse($resultSet) {
        throw new Exception("@todo");
    }
    
    /**
     *    Retorna a SQL criada dentro do objeto TableMapper
     *    @return string
     */
    public function getSql() {
        return $this->sql;
    }

    /**
     *    Retorna os campos da tempo
     *    @return array
     */
    public function getFields()
    {
    	$attrs = vita()->db->select("DESC `{$this->database}`.`{$this->tablename}`;");
    	$properties = array();

    	foreach ($attrs as $value) {
            $properties[$value->field] = $value;
            if($value->key == 'PRI') {
                $this->setPrimaryKey($value->field);
            }
    	}

    	return $properties;
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

    /**
     *    Remove um registro do banco de dados
     *    @param type $id
     *    @return type
     */
    public function del($id = 0)
    {
        $sql = "DELETE 
    	        FROM  `{$this->database}`.`{$this->tablename}` 
                WHERE `{$this->tablename}`.`{$this->primaryKey}` = :fid;";

    	vita()->db->query($sql);
    	vita()->db->bind(':fid', $id);
    	return vita()->db->execute();
    }
}
