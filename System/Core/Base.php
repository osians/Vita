<?php


namespace System\Core;

use PDO;
use Vita\Vita;


// TODO - precisa colocar opcao de executar queries com transaction

/**
 * Class Base
 *
 * Permite a criacao de Classes que fornecem apenas dados para
 * objetos como controllers.
 *
 * @package Vita\Core
 */
class Base
{
    /**
     * Database table name.
     * Needs be overwritten within the child class
     * @var string
     */
    protected $tableName = 'unknown_database_table_name';

    /**
     * Database table Columns name array
     * @var array
     */
    protected $columns = [];

    /**
     * Unique Instance
     *
     * @var Base
     */
    protected static $instance = null;

    /**
     * No Construct allowed
     */
    public function __construct()
    {
    }

    /**
     * Retorna a instância singleton do objeto
     */
    public static function getInstance()
    {
        $class = self::getCalledClass();
        if (self::$instance === null || !is_a(self::$instance, $class)) {
            self::$instance = new $class();
        }
        return self::$instance;
    }

    /**
     * Retorna o nome da classe
     * @return string
     */
    private static function getCalledClass()
    {
        if (function_exists('get_called_class')) {
            return get_called_class();
        }

        $bt = debug_backtrace();
        return $bt[2]['class'] . 'Base';
    }

    /**
     * Sql Query
     * @param string $sql
     * @return array - return Array of Objects
     */
    protected function query($sql)
    {
        $em = Vita::getInstance()->getEntityManager();
        $stm = $em->getConnection()->prepare($sql);
        $stm->execute();

        return ($stm->rowCount() > 0) ? $stm->fetchAll(PDO::FETCH_OBJ) : array();
    }

    /**
     * Execute SQL PDO Query
     * @param $sql
     * @param array $bind
     * @return bool
     */
    protected function execute($sql, $bind = array())
    {
        $em = Vita::getInstance()->getEntityManager();
        $stm = $em->getConnection()->prepare($sql);
        return $stm->execute($bind);
    }

    /**
     * Persista data into an SQL Database
     * @param array $dados
     * @return false|int
     */
    public function save($dados)
    {
        $table = $this->getTableName();
        $dados = is_object($dados) ? (array)$dados : $dados;

        $id = isset($dados["id_{$table}"]) ? $dados["id_{$table}"] : null;
        $isUpdate = isset($id) && $id > 0;
        $column = array();

        // update
        if ($isUpdate) {
            foreach ($dados as $key => $value) {
                $column[] = "{$key}=:{$key}";
            }

            $sql = "UPDATE {$table} SET " . implode(",", $column) . " WHERE id_{$table}=:id_{$table};";
        }

        // insert
        if (!$isUpdate) {
            $column = implode(",", array_keys($dados));
            $keys = implode(",", array_map(
                function ($val) {
                    return ":{$val}";
                },
                array_keys($dados)
            ));

            $sql = "INSERT INTO {$table} ({$column}) VALUES ($keys);";
        }

        if (!$this->execute($sql, $dados)) {
            return false;
        }

        return $isUpdate ? $id : Vita::getInstance()->getEntityManager()->getConnection()->lastInsertId();
    }

    /**
     * Remove or inactivate record
     * @param int $id
     * @param bool $inactiveOnly
     * @return bool
     */
    public function remove($id = 0, $inactiveOnly = true)
    {
        $table = $this->getTableName();

        $sql = "DELETE FROM {$table} WHERE id_{$table} = :id;";
        $dados = array('id' => $id);

        if ($inactiveOnly) {
            $sql = "UPDATE {$table} SET active=:active, inactive_date=:inactive_date WHERE id_{$table}=:id";
            $dados = array(
                'active' => 0,
                'inactive_date' => date('Y-m-d H:i:s'),
                'id' => $id
            );
        }

        return $this->execute($sql, $dados);
    }

    /**
     * Count total records
     * @param bool $activeOnly
     * @return int
     */
    public function count($activeOnly = true)
    {
        $where = $activeOnly ? ' active = 1 ' : ' 1 ';
        $sql = "select count(*) as total from {$this->getTableName()} where $where";
        $rs = $this->query($sql);
        return empty($rs) ? 0 : intval($rs[0]->total);
    }

    /**
     * Get results from database
     * @param array $where
     * @return array array of stdClass
     */
    public function get($where = array())
    {
        $condition = '';

        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $condition .= " AND {$key} = '{$value}'";
            }
        }

        $sql = "SELECT *
                FROM {$this->getTableName()}
                WHERE 1 {$condition};";

        return $this->query($sql);
    }

    /**
     * Get All records, active and inactive
     * @return array
     */
    public function getAll()
    {
        return $this->get(array());
    }

    /**
     * New Entity From Array
     * @param array $data
     * @return array
     */
    public function newFromArray($data = array())
    {
        $entity = new \stdClass();

        foreach ($this->getColumns() as $column) {
            $entity->$column = isset($data[$column]) ? $data[$column] : null;
        }

        return $entity;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return strtolowerr($this->tableName);
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }
}
