<?php

namespace Osians\DataEntityManager;

use \PDO;
use \Osians\DataEntityManager\QueryBuilder;

class EntityManager
{
	/**
	 *    PDO Database Drive
	 *    @var \PDO
	 */
	protected $_database = null;

	protected $_sqlStatement = null;

	protected $_debug = array();

	public function __construct(\PDO $database)
	{
		$this->_database = $database;
	}

	public function getErrorCode()
	{
		return $this->_database->errorCode();
	}

	public function getErrorInfo()
	{
		return $this->_database->errorInfo();
	}

    public function getLastInsertId()
    {
        return $this->_database->lastInsertId();
    }

    public function beginTransaction()
    {
        $this->_database->beginTransaction();
    }

    public function inTransaction()
    {
        return $this->_database->inTransaction();
    }

    public function commit()
    {
        $this->_database->commit();
    }

    public function rollback()
    {
        $this->_database->rollback();
    }

    public function prepare($query)
    {
        return $this->_database->prepare($query);
    }

    public function save(EntityInterface $entity)
    {
        if (null !== $entity->getId()) {
            return $this->update($entity);
        }
        $this->insert($entity);
    }

    public function update(EntityInterface $entity)
    {
        $original = clone $entity;
        $this->load($original, $entity->getId());

        $data = $entity->toArray();

        foreach ($original->toArray() as $key => $value) {
            if ($data[$key] == $value) {
                unset($data[$key]);
            }
        }

        if (count($data) == 0) {
            return true; /* nothing to do */
        }

        $query = new QueryBuilder();
        $query->update($entity->getTableName())
              ->set($data)
              ->where("{$entity->getPrimaryKeyName()} = ?", $entity->getId());

        return $this->_database->prepare($query->sql())->execute();
    }

    public function insert(EntityInterface $entity)
    {
        $data = $entity->toArray();

        $query = new QueryBuilder();
        $query->insert()
              ->into($entity->getTableName())
              ->values($data);

        return $this->_database->prepare($query->sql())->execute();
    }

    /**
     *    Remove uma Entidade da Base de dados
     */
    public function delete(EntityInterface $entity)
    {
        if (null == $entity->getId()) {
            return false;
        }

        $query = new QueryBuilder();
        $query->delete()
              ->from($entity->getTableName())
              ->where("{$entity->getPrimaryKeyName()} = ?", $entity->getId());

        $stmt = $this->_database->prepare($query->sql());
        $stmt->execute();

        if (!$stmt->rowCount()) {
            throw new Exception("Deletion failed");
        }

        $entity->setId(null);

        return true;
    }

    /**
     *    @param \EntityInterface $entity
     *    @param Integer $id
     */
    public function load(EntityInterface $entity, $id = 0)
    {
        $query = new QueryBuilder();
        $query->select()
              ->from($entity->getTableName())
              ->where("{$entity->getPrimaryKeyName()} = ?", $id);

        $stm = $this->_database->prepare($query->sql());
        $stm->execute();

        if ($stm->rowCount() > 0) {
            $row = $stm->fetch(PDO::FETCH_OBJ);
            $entity->init($row);
        }
    }

    /**
     *    fetch data and creates a Virtual Entity
     *
     *    @param  \QueryBuilderInterface $query
     *    @return \VirtualEntityInterface
     */
    public function fetch(QueryBuilderInterface $query)
    {
        $resultSet = array();
        $stm = $this->prepare($query);
        $stm->execute();

        if ($stm->rowCount() > 0) {
            $rows = $stm->fetchAll(\PDO::FETCH_OBJ);
            foreach ($rows as $row) {
                $ve = new VirtualEntity();
                $ve->init($row);
                $resultSet[] = $ve;
            }
        }

        return $resultSet;
    }

    /**
     *    fetch data of one Registry and creates a Virtual Entity
     *
     *    @param  \QueryBuilderInterface $query
     *    @return \VirtualEntityInterface | null
     */
    public function fetchOne(QueryBuilderInterface $query)
    {
        $query->count(1);
        $result = $this->fetch($query);
        return isset($result[0]) ? $result[0] : null;
    }

    /**
     *    Encontra todos os Registros de uma tabela limitados a 100 por padrão
     *
     *    @param  QueryBuilderInterface $query
     *    @return Array of Entity
     */
    public function findAll(QueryBuilderInterface $query)
    {   
    	$this->_setDebugRawsql($query->sql());
    	$stm = $this->_database->prepare($query->sql());
        $stm->execute();

        $entities = array();
        if ($stm->rowCount() > 0) {
            foreach ($stm->fetchAll(PDO::FETCH_OBJ) as $row) {
            	$entityClass = ucfirst($query->getTableName());
                $entity = new $entityClass();
                $entity->setEntityManager($this)->init($row);
                array_push($entities, $entity);
            }
        }

        return $entities;
    }

    /**
     *    Retorna o primeiro registro encontrado em uma dada tabela
     *
     *    @param  String $tableName
     *    @param  array  $criteria
     *
     *    @return Entity
     */
    public function findOne(QueryBuilderInterface $QueryBuilder)
    {
        $QueryBuilder->count(1);
    	$result = $this->findAll($QueryBuilder);
    	return isset($result[0]) ? $result[0] : null;
    }

    public function loadOnetoOne(EntityInterface $entity, $referencia)
    {
    	$query = new QueryBuilder();
    	$query->from($entity->getTableName());
    	$query->innerJoin(
    		"$referencia as parent", 
    		"categoria.id_categoria = parent.id_categoria");

    	return [$entity, $referencia];
    }

    public function loadOnetoN()
    {
    }

    public function loadNtoOne()
    {
    	// Tem a ver com Relacionamentos!
    	
    	// 1-1
    	// select * from categoria 
    	// join categoria as parent on categoria.id_categoria = parent.id_categoria;

    	// 1-n
    	// select * from categoria 
    	// join categoria as parent on categoria.id_parent = parent.id_categoria;
    	
    }

    private function _setDebugRawsql($sql)
    {
    	$this->_debug['rawsql'] = $sql;
    	return $this;
    }

    private function _addDebugParametro($param)
    {
		$this->_debug['parametros'][] = $param;
    	return $this;
    }

    public function debug()
    {
    	print_r($this->_debug);
    }
}
