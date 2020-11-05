<?php

namespace Osians\VeManager;

use \Osians\VeManager\VirtualEntity;
use \Osians\VeManager\QueryBuilder;

/**
 * Virtual Entity Manager
 *
 * @author Wanderlei Santana <sans.pds@gmail.com>
 * @package VEM - Virtual Entity Manager
 * @version 20200622220300
 */
class VeManager
{
    /**
     * PDO Connection
     *
     * @var \PDO
     */
    protected $_connection = null;
    
    /**
     * Construct
     *
     * @param \PDO $conn
     */
    public function __construct(\PDO $conn)
    {
        $this->setConnection($conn);
    }

    /**
     * Set PDO Connection to be used
     *
     * @param \PDO $conn [description]
     *
     * @return  VeManager
     */
    public function setConnection(\PDO $conn)
    {
        $this->_connection = $conn;
        return $this;
    }

    /**
     * Get PDO Connection
     *
     * @return \PDO
     *
     * @throws String [<description>]
     */
    public function getConnection()
    {
        if (null === $this->_connection) {
            throw new Exception("PDO Connection is missing", 1);
        }

        return $this->_connection;
    }
    
    /**
     * Prepare an SQL Query
     *
     * @param  QueryBuilderInterface $query
     *
     * @return Stmt
     */
    protected function _prepare(QueryBuilderInterface $query)
    {
        return $this->getConnection()->prepare($query);
    }

    /**
     * Execute Query on Database
     *
     * @param \Osians\VeManager\QueryBuilderInterface $query
     * @param bool $asArray - true return PDO StdClass Array
     *
     * @return Array of VirtualEntity
     */
    public function query(QueryBuilderInterface $query, $asArray = false)
    {
        $resultSet = array();
        $stm = $this->_prepare($query);
        $stm->execute();

        if (!$stm->rowCount() > 0) {
            return $resultSet;
        }

        $rows = $stm->fetchAll(\PDO::FETCH_OBJ);

        // returns simple Array of StdClass
        if ($asArray) {
            return $rows;
        }

        // returns  array of VirtualEntity
        foreach ($rows as $row) {
            $resultSet[] = $this->_newEntityFromData($query, $row);
        }

        return $resultSet;
    }

    /**
     * Alias for Query Method
     *
     * @param \Osians\VeManager\QueryBuilderInterface $qb
     * @param bool $asArray
     *
     * @return Array of VirtualEntity
     */
    public function fetch(QueryBuilderInterface $qb, $asArray = false)
    {
        return $this->query($qb, $asArray);
    }
    
    /**
     * Query database for only one record
     *
     * @param \Osians\VeManager\QueryBuilderInterface $query
     *
     * @return VirtualEntity
     */
    public function fetchOne(QueryBuilderInterface $query)
    {
        $rs = $this->query($query);
        return count($rs) > 0 ? $rs[0] : $rs;
    }

    /**
     * Returns Entity By its ID
     *
     * @param string $tablename
     * @param integer $id
     *
     * @return VirtualEntity
     */
    public function get($tablename, $id = 0)
    {
        $query = new QueryBuilder();
        $query->select()->from($tablename)->where("id_{$tablename} = ?", $id)->limit(1);
        return $this->fetchOne($query);
    }
    
    /**
     * Returns Entity from Database By Table Column name
     *
     * @param string $tablename
     * @param string $columnName
     * @param mixed $value
     *
     * @return Array of VirtualEntity
     */
    public function getBy($tablename, $columnName, $value = null)
    {
        $query = new QueryBuilder();
        $query->select()->from($tablename)->where("$columnName = ?", $value);
        return $this->fetch($query);
    }


    /**
     * Remove a Entity
     *
     * @param \Osians\VeManager\EntityInterface $entity
     * 
     * @return bool
     */
    public function delete(EntityInterface $entity)
    {
        if ($entity->getId() == null) {
            return true;
        }
     
        $query = new QueryBuilder();
        $query->delete()
              ->from($entity->getTableName())
              ->where($entity->getPrimaryKeyName() . " = ?", $entity->getId());
        
        $stmt = $this->_prepare($query);
        $stmt->execute();

        if (!$stmt->rowCount()) {
            return false;
        }

        $entity->setId(null);
        
        return true;
    }
                
    /**
     * Persist Data
     *  
     * @param EntityInterface $entity
     *
     * @return integer|false - Last Insert ID or FALSE (case error)
     */
    public function save(EntityInterface $entity)
    {
        if ($entity->getQueryBuilder() == null) {
            $entity->setQueryBuilder(new QueryBuilder());
        }
        
        if (null == $entity->getId()) {
            return $this->_saveNewRecord($entity);
        }

        return $this->_saveExistingRecord($entity);
    }
    
    /**
     * Insert a new Record into table
     *
     * @param  EntityInterface $entity
     *
     * @return false | last_inserted_id
     */
    protected function _saveNewRecord(EntityInterface $entity)
    {
        $values = array();
        foreach ($entity->getChangedProperty() as $column => $struct) {
            $values[$column] = ($struct['to'] instanceof EntityInterface)
                ? $struct['to']->getId()
                : $struct['to'];
        }

        if (empty($values)) {
            return false;
        }

        $qb = new QueryBuilder();
        $qb->insert()->into($entity->getTableName())->values($values);

        $stm = $this->getConnection()->prepare($qb->sql());
        $stm->execute();

        $entity->setId($this->getConnection()->lastInsertId());
        return $this->getConnection()->lastInsertId();
    }


    /**
     * Update a record into table database
     *
     * @param  EntityInterface $entity
     *
     * @return bool
     */
    protected function _saveExistingRecord(EntityInterface $entity)
    {
        $camposAlterados = $entity->getChangedProperty();

        $tables = $entity->getQueryBuilder()->getUsedTables();

        if (empty($tables)) {
            $tables[] = $entity->getTableName();
        }

        foreach ($tables as $table) {

            $sets = array();

            foreach ($camposAlterados as $column => $struct) {
                if ($struct['id'] == null || $table != $struct['owner']) {
                    continue;
                }
                $sets["{$column} = ?"] = ($struct['to'] instanceof EntityInterface)
                    ? $struct['to']->getId()
                    : $struct['to'];
            }

            if (empty($sets)) {
                continue;
            }

            $qb = new QueryBuilder();
            $qb->update($table);

            foreach ($sets as $key => $value) {
                $qb->set($key, $value);
            }

            $qb->where("{$struct['pk']} = ?", $struct['id']);

            $stm = $this->getConnection()->prepare($qb->sql());
            $stm->execute();
        }

        return $this;
    }


    /**
     * Creates a new Virtual Entity from Database Table
     *
     * @param  String $tablename
     *
     * @return VirtualEntity
     */
    public function create($tablename)
    {
        $desc = $this->getTableDesc($tablename);

        $obj = new \StdClass;
        foreach (array_keys($desc) as $property) {
            $obj->$property = null;
        }

        $query = new QueryBuilder;
        $query->from($tablename);
        return $this->_newEntityFromData($query, $obj);
    }

    /**
     * Construct a Instance of a Virtual Entity
     *
     * @param QueryBuilderInterface $query
     * @param StdClass $data - data
     *
     * @return VirtualEntity
     */
    protected function _newEntityFromData($query, $data)
    {
        $ve = new VirtualEntity();
        $ve->init($data);
        $ve->setTablename($query->getTableName());
        $ve->setQueryBuilder($query);
        $ve->setEntityManager($this);
        
        return $ve;
    }
    
    /**
     * Returns DESC command from a Database Table
     *
     * @param string $tablename
     *
     * @return array
     */
    public function getTableDesc($tablename)
    {
        try {
            $stm = $this->getConnection()->prepare("DESC `{$tablename}`");
            $stm->execute();
        } catch (\Exception $e) {
            return false;
        }
        
        $properties = null;

        if ($stm->rowCount() > 0) {
            $rows = $stm->fetchAll(\PDO::FETCH_OBJ);

            foreach ($rows as $key => $value) {
                $properties[$value->Field] = $value;
            }
        }

        return $properties;
    }
    
    /**
     * Check if table exists in the Database
     *
     * @param string $tablename
     *
     * @return bool
     */
    public function tableExists($tablename)
    {
        $sh = $this->getConnection()->prepare("DESCRIBE `{$tablename}`");
        return ($sh->execute()) ? true : false;
    }
}
