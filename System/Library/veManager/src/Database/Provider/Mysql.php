<?php

namespace Osians\VeManager\Database\Provider;

use \PDO;
use \Exception;

class Mysql implements ProviderInterface
{
    protected $_hostname = null;

    protected $_port = null;

    protected $_username = null;

    protected $_password = null;

    protected $_databaseName = null;

    protected $_dbh = null;

    protected $_error = null;

    protected $_charset = null;

    public function __construct()
    {
        $this->_charset = 'utf8';
        $this->_port = 3306;
    }

    public function setHostname($value) 
    {
        $this->_hostname = $value;
        return $this;
    }

    public function getHostname()
    {
        return $this->_hostname;
    }

    public function setPort($value)
    {
        $this->_port = $value;
        return $this;
    }

    public function getPort()
    {
        return $this->_port;
    }

    public function setUsername($value)
    {
        $this->_username = $value;
        return $this;
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function setPassword($value)
    {
        $this->_password = $value;
        return $this;
    }

    public function getPassword()
    {
        return $this->_password;
    }

    public function setDatabaseName($value)
    {
        $this->_databaseName = $value;
        return $this;
    }

    public function getDatabaseName()
    {
        return $this->_databaseName;
    }

    public function setCharset($value)
    {
        $this->_charset = $value;
        return $this;
    }

    public function getCharset()
    {
        return $this->_charset;
    }

    public function getConnectionString()
    {
        return "mysql:host={$this->getHostname()};
                dbname={$this->getDatabaseName()};
                charset={$this->getCharset()};";
    }

    protected function _getOptions()
    {
        return array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->getCharset()}'"
        );
    }

    /**
     * @return \PDO
     * @throws Exception
     * @see ProviderInterface::conectar()
     */
    public function connect()
    {
        try {
            return new PDO(
                $this->getConnectionString(), 
                $this->getUsername(), 
                $this->getPassword(), 
                $this->_getOptions()
            );
        } catch (\PDOException $e) {
            $this->_throwException($e);
        }
    }

    protected function _throwException(\PDOException $e)
    {
        $code = $e->getCode();
        $message = $e->getMessage();

        switch ($code) {
            case 1049:
                throw new Exception(
                    "The database '{$this->getDatabaseName()}' does not exist.", $code);
            break;
            
            case 2002:
                throw new Exception(
                    "Database connection with '{$this->getHostname()}' was refused.", $code);
            break;

            case 1045:
                throw new Exception(
                    "Attempt to log into database '{$this->getHostname()}' with the username '{$this->getUsername()}' failed. The username or password may be incorrect.", $code);
            break;

            default:
                throw new Exception($message, $code);
            break;
        }
    }
}
