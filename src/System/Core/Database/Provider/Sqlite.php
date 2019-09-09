<?php

namespace Vita\System\Core;

class Sqlite implements DBProvider
{
    private $sqliteDbFolder = SYS_SQLITE_DB_folder;
    private $sqliteDbname = SYS_SQLITE_DBNAME;
    private $dbh = null;

    public function __construct(
        $sqliteDbFolder = null,
        $sqliteDbname = null
    ) {
        if ($sqliteDbFolder != null) {
            $this->sqliteDbFolder = $sqliteDbFolder;
        }
        if ($sqliteDbname  != null) {
            $this->sqliteDbname  = $sqliteDbname;
        }
    }

    public function setDBPath($value){$this->sqliteDbFolder = $value;}
    public function setDBName($value){$this->sqliteDbname = $value ;}

    public function conectar()
    {
        $__dsn__ = 'sqlite:'.$this->sqliteDbFolder.$this->sqliteDbname;

        try {
            $this->dbh = new \PDO($__dsn__);
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            //$memory_db = new \PDO('sqlite::memory:');
            //$memory_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->dbh;
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
        }
    }
}