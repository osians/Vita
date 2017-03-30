<?php if ( ! defined('ALLOWED')) exit('Acesso direto ao arquivo nao permitido.');

/**
 *
 * CLASSE SYS_DB RESPONSAVEL POR FAZER A INTERFACE COM O BANCO DE DADOS,
 * NO CASO, O PADRAO E' USO COM MYSQL, POREM, PODENDO SER USADO COM SQLITE.
 * OUTRAS INTERFACES PODEM SER ADICIONADAS DE FORMA SIMPLES.
 *
 *
 * [MODO DE USO]
 *
 * # exemplo 1 (Usando valores Default, definidos no arquivo de Config)
 * $database = DBFactory::create( 'MySQL' );
 *
 * # exemplo 2 (definindo valores manualmente )
 * $database = DBFactory::create( 'MySQL', array('host' => '127.0.0.1','dbport' => '3306','user' => 'wandeco','pass' => 'sans','dbname' => 'nome_banco_dados') );
 *
 * # exemplo 3 (Usando valores Default, definidos no arquivo de Config)
 * $database = DBFactory::create( 'SQLite' );
 *
 * # exemplo 4 (definindo valores manualmente )
 * $database = DBFactory::create( 'SQLite', array( 'dbpath' => '/caminho/arquivo/','dbname' => 'database.sqlite' ) );
 *
 * # Inserindo registros ...
 * $database->query('INSERT INTO mytable (FName, LName, Age, Gender) VALUES (:fname, :lname, :age, :gender)');
 *
 * $database->bind(':fname', 'John');
 * $database->bind(':lname', 'Smith');
 * $database->bind(':age', '24');
 * $database->bind(':gender', 'male');
 *
 * $database->execute();
 *
 * echo $database->lastInsertId();
 *
 * ================================= SELECT ====================
 * $database->query('SELECT FName, LName, Age, Gender FROM mytable WHERE FName = :fname');
 * $database->bind(':fname', 'Jenny');
 * $row = $database->single();
 *
 * @author Wanderlei Santana <sans.pds@gmail.com>
 * @package sys_db
 *
 */

class DBException extends SYS_Exception{}

class QueryException extends DBException{}

interface DBProvider
{
    public function conectar();
}

class MySQLProvider implements DBProvider
{
    // @ default params
    private $host   = null;
    private $dbport = null;
    private $user   = null;
    private $pass   = null;
    private $dbname = null;
    private $dbh    = null;
    private $error  = null;
    private $charset= null;

    /**
     * [__construct description]
     * @param [type] $__host__   [description]
     * @param [type] $__dbport__ [description]
     * @param [type] $__user__   [description]
     * @param [type] $__pass__   [description]
     * @param [type] $__dbname__ [description]
     */
    public function __construct(
        $__host__ = null,
        $__dbport__ = null,
        $__user__ = null,
        $__pass__ = null,
        $__dbname__ = null,
        $__charset__ = null)
    {
        if($__host__   != null ) $this->host   = $__host__;
        if($__user__   != null ) $this->user   = $__user__;
        if($__pass__   != null ) $this->pass   = $__pass__;
        if($__dbname__ != null ) $this->dbname = $__dbname__;
        if($__dbport__ != null ) $this->dbport = $__dbport__;

        $this->charset = ($__charset__ != null ) ? $__charset__ : 'utf8';
    }

    public function setHost( $__value__ ){$this->host = $__value__ ; }
    public function setDbport( $__value__ ){$this->dbport = $__value__ ; }
    public function setUser( $__value__ ){$this->user = $__value__ ; }
    public function setPass( $__value__ ){$this->pass = $__value__ ; }
    public function setDbname( $__value__ ){$this->dbname = $__value__ ; }

    public function conectar()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=' . $this->charset;
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT  => true,
            PDO::ATTR_ERRMODE     => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->charset}'"
        );

        try{
            return new PDO($dsn, $this->user, $this->pass, $options);
        }
        catch( PDOException $e )
        {
            switch($e->getCode()){
                case 1049: throw new DBException( 'O Banco de dados "' . $this->dbname . '" não existe.', $e->getCode() );break;
                case 2002: throw new DBException( 'Conexão com o servidor de banco de dados "' . $this->host . '" foi recusada.', $e->getCode() );break;
                case 1045: throw new DBException( 'Falha ao tentar logar no servidor de banco de dados "'.$this->host.'" com o usuário "' . $this->user . '". Talvez a senha ou o nome de usuário estejam incorretos.', $e->getCode() );break;
                default:
                    throw new DBException( $e->getMessage() , $e->getCode() );
                break;
            }
            #$this->error = $e->getMessage();
            #print( $e->getMessage() );
            #print( $e->getCode() );
        }
    }
}

class SQLliteProvider implements DBProvider
{
    private $sqlite_db_folder = SYS_SQLITE_DB_folder;
    private $sqlite_dbname = SYS_SQLITE_DBNAME;
    private $dbh = null;

    public function __construct( $__sqlite_db_folder__ = null, $__sqlite_dbname__ = null)
    {
        if($__sqlite_db_folder__ != null) $this->sqlite_db_folder = $__sqlite_db_folder__ ;
        if($__sqlite_dbname__  != null) $this->sqlite_dbname  = $__sqlite_dbname__ ;
    }

    public function setDBPath($__value__){$this->sqlite_db_folder = $__value__;}
    public function setDBName($__value__){$this->sqlite_dbname = $__value__ ;}

    public function conectar()
    {
        $__dsn__ = 'sqlite:'.$this->sqlite_db_folder.$this->sqlite_dbname;

        try{
            $this->dbh = new PDO($__dsn__);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //$memory_db = new PDO('sqlite::memory:');
            //$memory_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->dbh;
        }
        catch( PDOException $e ){
            $this->error = $e->getMessage();
        }
    }
}

class SYS_Db
{
    private $dbh;
    protected $error;

    protected $stmt;

    public function __construct( $conn = null ){
        if($conn != null){
            $this->setConn( $conn );
        }
    }

    public function setConn($dbh){$this->dbh = $dbh ;}

    public function getDb(){return $this->dbh;}

    public function query($query){
        $this->stmt = $this->dbh->prepare($query);
        return $this->stmt;
    }
    /*
        Para consultas tal como : 
        $sql = 'SELECT name, color, calories FROM fruit ORDER BY name';
        foreach ($conn->query($sql) as $row) {
            print $row['name'] . "\t";
            print $row['color'] . "\t";
            print $row['calories'] . "\n";
        }
    */
    public function sql( $__sql_query ){
        return $this->dbh->query( $__sql_query );
    }

    public function bind($param, $value, $type = null){
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    private function parseAsObject( $__first_only__ = false )
    {
        $objeto = new stdClass;
        $retorno = array();
        $result = ($__first_only__) ? $this->single() : $this->resultset();

        if($result === false) return false;

        # se primeira row apenas ...
        if($__first_only__)
        {
            foreach ($result as $key => $value) {
                $oKey = strtolower( $key );
                $objeto->$oKey = $value;
            }
            return $objeto;
        }

        foreach ($result as $row )
        {
            $objeto = new stdClass;
            foreach( $row as $key=>$value ):
                $oKey = strtolower( $key );
                $objeto->$oKey = $value;
            endforeach;
            $retorno[] = $objeto;
        }

        return $retorno;
    }

    public function select_first( $__sql__, $_bind_ = array() )
    {
        return $this->select($__sql__,$_bind_,true,true);
    }

    public function select(
        $__sql__,
        $_bind_ = array(),
        $__as_object__ = true,
        $__first_only__ = false
    ){
        $this->query($__sql__);
        if(count($_bind_)>0):
            foreach ($_bind_ as $key => $value) {
                $this->bind( $key, $value );
            }
        endif;

        $this->execute();

        if( $__as_object__ ):
            return $this->parseAsObject( $__first_only__ );
        else:
            if($__first_only__){
                return $this->single();
            }else{
                return $this->resultset();
            }
        endif;
    }

    /**
     * [insert description]
     * Ex. de uso : $rs = $database->insert(
     *   'INSERT INTO mytable (FName, LName, Age, Gender) VALUES (:fname, :lname, :age, :gender)',
     *   array(':fname' => 'John',':lname'=>'Smith',':age','24',':gender'=>'male'),
     *   true
     * );
     * @param  [type]  $__sql__                     [description]
     * @param  array   $_bind_                      [description]
     * @param  boolean $__return_last_inserted_id__ [description]
     * @return [type]                               [description]
     */
    public function insert(
        $__sql__,
        $_bind_ = array(),
        $__return_last_inserted_id__ = true )
    {
        $this->query( $__sql__ );

        if(count($_bind_)>0):
            foreach ($$_bind_ as $key => $value) {
                $this->bind( $key, $value );
            }
        endif;

        $this->execute();

        return ($__return_last_inserted_id__) ? $this->lastInsertId() : true;
    }

    /**
     * Executa um update e retorna o numero de registros alterados
     * @param string - SQL a ser executada
     * @return int
     **/
    public function update( $__sql__ ){
        return $this->executar($__sql__);
    }

    public function execute(){
        try{
            return $this->stmt->execute();
        }catch(PDOException $e){
            throw new DBException( $e->getMessage() , $e->getCode() );
        }
    }

    /**
     *
     * @doc http://php.net/manual/pt_BR/pdo.exec.php
     *
     * @param  [type] $__query__ [description]
     * @return [type]            [description]
     */
    public function executar( $__query__ )
    {
        return $this->dbh->exec( $__query__ );
    }

    public function resultset(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function single(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function rowCount(){
        $this->execute();
        return $this->stmt->rowCount();
    }

    public function lastInsertId(){
        return $this->dbh->lastInsertId();
    }

    public function beginTransaction(){
        return $this->dbh->beginTransaction();
    }

    public function commit(){
        return $this->dbh->commit();
    }

    public function roolback(){
        return $this->dbh->rollBack();
    }

    public function debugDumpParams(){
        return $this->stmt->debugDumpParams();
    }
}

class DBFactory
{
    /**
    * @param string  - Tipo de banco de dados a ser criado conexao
    * @return object - Objeto de conexao ao DB escolhido
    */
    public static function create($__db__ = 'MySQL')
    {
        $_args_ = func_get_args();
        $_extra_args = isset($_args_[1]) ? $_args_[1] : null;

        $__db__ = strtolower($__db__);

        switch ($__db__) {
            case 'mysql':

                $db = new MySQLProvider();

                if( isset($_extra_args['host'])&&
                    isset($_extra_args['port'])&&
                    isset($_extra_args['user'])&&
                    isset($_extra_args['pass'])&&
                    isset($_extra_args['dbname']) )
                {
                    $db->setHost( $_extra_args['host'] );
                    $db->setDbport( $_extra_args['port'] );
                    $db->setUser( $_extra_args['user'] );
                    $db->setPass( $_extra_args['pass'] );
                    $db->setDbname( $_extra_args['dbname'] );
                }

                $conn = $db->conectar();
                return new SYS_Db( $conn );
            break;

            case 'sqllite':
                $db = new SQLliteProvider();

                if(isset($_extra_args['dbpath'])&&isset($_extra_args['dbname']))
                {
                    $db->setDBPath( $_extra_args['dbpath'] );
                    $db->setDBName( $_extra_args['dbname'] );
                }

                $conn = $db->conectar();
                return new SYS_Db( $conn );
            break;

            default:
                return new stdClass(); # objeto null
            break;
        }
    }
}