<?php

namespace Osians\DataEntityManager\Database\Provider;

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
            PDO::ATTR_PERSISTENT  => true,
            PDO::ATTR_ERRMODE     => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->getCharset()}'"
        );
    }

    /**
     *    @see ProviderInterface::conectar()
     *    @return \PDO
     */
    public function conectar()
    {
        try {
            return new PDO(
                $this->getConnectionString(), 
                $this->getUsername(), 
                $this->getPassword(), 
                $this->_getOptions()
            );
        } catch (PDOException $e) {
            $this->_throwException($e);
        }
    }

    protected function _throwException(PDOException $e)
    {
        $code = $e->getCode();
        $message = $e->getMessage();

        switch ($code) {
            case 1049:
                throw new Exception(
                    "O Banco de dados {$this->getDatabaseName()} não existe.", $code);
            break;
            
            case 2002:
                throw new Exception(
                    "Conexão com o servidor de banco de dados {$this->getHostname()} foi recusada.", $code);
            break;

            case 1045:
                throw new Exception(
                    "Falha ao tentar logar no servidor de banco de dados {$this->getHostname()} com o usuário {$this->getUsername()}. Talvez a senha ou o nome de usuário estejam incorretos.", $code);
            break;

            default:
                throw new Exception($message, $code);
            break;
        }
    }
}
