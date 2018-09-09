<?php

namespace Framework\Vita\Core;

if (!defined('ALLOWED')) {
    exit('Acesso direto ao arquivo nao permitido.');
}

class SystemCoreDatabaseProviderMysql implements DBProvider
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
            \PDO::ATTR_PERSISTENT  => true,
            \PDO::ATTR_ERRMODE     => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->charset}'"
        );

        try{
            return new \PDO($dsn, $this->user, $this->pass, $options);
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
