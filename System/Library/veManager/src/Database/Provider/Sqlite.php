<?php

namespace Osians\VeManager\Database\Provider;

use \PDO;
use \Exception;

class Sqlite implements ProviderInterface
{
    protected $_folder = null;
    
    protected $_name = null;
    
    protected $_dbh = null;

    public function __construct($folder = null, $name = null)
    {
        $this->setFolder($folder);
        $this->setName($name);
    }

    public function setFolder($folder)
    {
        $this->_folder = $folder;
        return $this;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @see ProviderInterface::conectar()
     * @return \PDO
     */
    public function connect()
    {
        $dsn = "sqlite:{$this->_folder}{$this->_name}";

        try
        {
            $this->_dbh = new \PDO($dsn);
            $this->_dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->_dbh;
        }
        catch (\PDOException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }    
    }
}
