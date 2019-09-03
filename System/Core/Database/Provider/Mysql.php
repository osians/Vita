<?php

namespace Vita\System\Core\Database\Provider;

use \Vita\System\Core\Database\ProviderInterface;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ProviderInterface.php';

class Mysql implements ProviderInterface
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
     * @param [type] $host   [description]
     * @param [type] $dbport [description]
     * @param [type] $user   [description]
     * @param [type] $pass   [description]
     * @param [type] $dbname [description]
     */
    public function __construct(
        $host = null,
        $dbport = null,
        $user = null,
        $pass = null,
        $dbname = null,
        $charset = null
    ) {
        if ($host   != null) {$this->host   = $host;}
        if ($user   != null) {$this->user   = $user;}
        if ($pass   != null) {$this->pass   = $pass;}
        if ($dbname != null) {$this->dbname = $dbname;}
        if ($dbport != null) {$this->dbport = $dbport;}

        $this->charset = ($charset != null ) ? $charset : 'utf8';
    }

    public function setHost($value) {$this->host = $value; }
    public function setDbport($value) {$this->dbport = $value; }
    public function setUser($value) {$this->user = $value; }
    public function setPass($value) {$this->pass = $value; }
    public function setDbname($value) {$this->dbname = $value; }

    public function conectar()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=' . $this->charset;
        // Set options
        $options = array(
            \PDO::ATTR_PERSISTENT  => true,
            \PDO::ATTR_ERRMODE     => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->charset}'"
        );

        try {
            return new \PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {

            switch ($e->getCode()) {
                case 1049:
                    throw new DBException(
                        "O Banco de dados {$this->dbname} não existe.",
                        $e->getCode()
                    );
                break;
                
                case 2002:
                    throw new DBException(
                        "Conexão com o servidor de banco de dados 
                        {$this->host} foi recusada.",
                        $e->getCode()
                    );
                break;

                case 1045:
                    throw new DBException(
                        "Falha ao tentar logar no servidor de banco de dados {$this->host} com o usuário {$this->user}. Talvez a senha ou o nome de usuário estejam incorretos.",
                        $e->getCode()
                    );
                break;

                default:
                    throw new DBException($e->getMessage(), $e->getCode());
                break;
            }
            #$this->error = $e->getMessage();
            #print( $e->getMessage() );
            #print( $e->getCode() );
        }
    }
}
