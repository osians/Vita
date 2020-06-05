<?php

namespace Osians\DataEntityManager;

/**
 *    Query Builder
 */
class QueryBuilder implements QueryBuilderInterface
{
	/**
	 * @var String - Select, Insert, Delete, Update
	 */
	protected $_action = null;

	/**
	 *    Nome da tabela principal em que a consulta ocorre
	 *    @var Array||String
	 */
	protected $_from = null;

	protected $_fields = null;

	protected $_join = array();

	protected $_where = array();

	protected $_group = null;

	protected $_order = null;

	/**
	 *    @var Interger
	 */
	protected $_offset = null;

	/**
	 *    @var Interger
	 */
	protected $_count = 1000;

	/**
	 *    Constructor
	 *    @param String $table - Nome da Tabela a consultar
	 */
	public function __construct()
	{
	}

	public function select($fields = array())
	{
		$this->_addFields($fields);
		$this->_action = 'SELECT';
		return $this;
	}

	public function delete()
	{
		$this->_action = 'DELETE';
		return $this;
	}

	public function update($tableName)
	{
		$this->_from = $tableName;
		$this->_action = 'UPDATE';
		return $this;
	}

	public function insert()
	{
		$this->_action = 'INSERT';
		return $this;
	}

	public function set($set)
	{
		$this->_set = $set;
		return $this;
	}

	public function values($values)
	{
		$this->_values = $values;
		return $this;
	}

	protected function _addFields($fields = array(), $alias = null)
	{
		if (is_array($fields) && sizeof($fields) > 0) {
			foreach ($fields as $key => $value) {

				$value = $this->_delimitarValue($value, $alias);

				if (!is_numeric($key)) {
					$value = "{$value} AS `{$key}`";
				}

				$this->_fields[] = $value;
			}
		}
		return $this;
	}

	/**
	 *    Adicionar Delimitadores (`) a valores
	 *
	 *    @param String $value
	 *    @param String $alias
	 *    @return String
	 */
	private function _delimitarValue($value, $alias = null)
	{
		if (null != $alias && strpos($value, $alias) === false) {
			return "`{$alias}`.`{$value}`";
		}

		if ((null != $alias && strpos($value, $alias) === 0) ||
		    (null == $alias && strpos($value, ".") > 0) ||
		    (null != $alias && strpos($value, $alias) > 0)) {
			list($alias, $field) = explode(".", $value);
			return "`{$alias}`.`{$field}`";
		}

		return $value;
	}

	/**
	 *    Tabela em que as alterações acontecerao
	 *
	 *    @param String $table - Tablename
	 *    @return \QueryBuilder
	 */
	public function from($table)
	{
		$this->_from = $table;
		return $this;
	}

	/**
	 *   @alias para from
	 */
	public function into($table)
	{
		return $this->from($table);
	}

	public function innerJoin($table, $on, $fields = array())
	{
		$alias = is_array($table) ? array_keys($table) : [null];
		$this->_addFields($fields, $alias[0]);

		$this->_join[] = array(
			'type' => 'INNER', 
			'table' => $table, 
			'on' => $on
		);

		return $this;
	}

	public function leftJoin($table, $on, $fields = array())
	{
		$alias = is_array($table) ? array_keys($table) : [null];
		$this->_addFields($fields, $alias[0]);

		$this->_join[] = array(
			'type' => 'LEFT', 
			'table' => $table, 
			'on' => $on
		);

		return $this;
	}

	public function where()
	{
		$this->_where[] = func_get_args();
		return $this;
	}

	public function group($group)
	{
		$this->_group = $group;
		return $this;
	}

	public function order($order)
	{
		$this->_order = $order;
		return $this;
	}

	public function limit($limit)
	{
		die("@todo EntityQuery::limit()");
	}

	public function offset($offset = 0)
	{
		$this->_offset = $offset;
		return $this;
	}

	public function count($count)
	{
		$this->_count = $count;
		return $this;
	}

	/**
	 *    @see  EntityQueryInterface::getTableName()
	 *    @return String
	 */
	public function getTableName()
	{
		return $this->_from;
	}

	public function sql()
	{
		switch ($this->_action) {

			case 'SELECT':
				return "{$this->_parseAction()} {$this->_parseFields()} FROM {$this->_parseFrom()} {$this->_parseJoins()} {$this->_parseWhere()} {$this->_parseGroup()}{$this->_parseOrder()}{$this->_parseLimit()}";
				break;
			
			case 'DELETE':
				return "{$this->_parseAction()} FROM {$this->_parseFrom()} {$this->_parseJoins()} {$this->_parseWhere()} {$this->_parseLimit()}";
				break;

			case 'UPDATE':
				return "{$this->_parseAction()} {$this->_parseFrom()} {$this->_parseSet()} {$this->_parseWhere()} {$this->_parseLimit()}";
				break;

			case 'INSERT':
				return "{$this->_parseAction()} INTO {$this->_parseFrom()} {$this->_parseInsertValues()}";
				break;

			default: break;
		}
	}

	public function _parseAction()
	{
		if (is_null($this->_action)) {
			throw new \Exception("'Kind of Action' is missing");
		}
		return $this->_action;
	}

	protected function _parseFrom()
	{
		if (is_null($this->_from)) {
			throw new \Exception("'FROM' property is missing");
		}

		if (is_array($this->_from)) {
			$alias = array_keys($this->_from);
			$table = array_values($this->_from);

			$from = ($table[0] instanceof EntityQueryInterface) 
				? "({$table[0]->sql()})" : "`{$table[0]}`";

			return "{$from} AS `{$alias[0]}`";
		}

		return "`{$this->_from}`";
	}

	protected function _parseFields()
	{
		$fromAlias = is_array($this->_from) ? array_keys($this->_from) : [$this->_from];

		if (!is_array($this->_fields)) {
			return "`{$fromAlias[0]}`.`*`";
		}
		
		if (count($this->_fields) == 0 || $this->_fields[0] == '*') {
			return "`{$fromAlias[0]}`.`*`";
		}

		return implode(", ", $this->_fields);
	}

	protected function _parseJoins()
	{
		if (empty($this->_join)) {
			return '';
		}

		$statement = array();
		foreach ($this->_join as $join) {
			$alias = is_array($join['table']) ? array_keys($join['table']) : null;
			$table = is_array($join['table']) ? array_values($join['table']) : [$join['table']];

			if (!is_null($alias)) {
				$alias = "AS {$alias[0]}";
			}

			$statement[] = "{$join['type']} JOIN {$table[0]} {$alias} ON {$join['on']}";
		}

		return implode(' ', $statement);
	}

	protected function _parseWhere()
	{
		if (empty($this->_where)) {
			return '';
		}

		$statement = [];

		foreach ($this->_where as $where) {

			if (strpos($where[0], '?') === false) {
				$statement[] = "({$where[0]})";
				continue;
			}

			$stmt = $where[0];
			array_shift($where);
			foreach ($where as $replace) {
				$replace = is_numeric($replace) ? $replace : "'{$replace}'";
				$stmt = preg_replace('/\?/i', $replace, $stmt, 1);
			}

			$statement[] = "({$stmt})";
		}

		return 'WHERE ' . implode(' AND ', $statement);
	}

	protected function _parseSet()
	{
		if (!is_array($this->_set) || empty($this->_set)) {
			throw new \Exception("SET values are missing", 1);			
		}

		$retorno = array();
		foreach ($this->_set as $key => $value) {
			$retorno[] = "`{$key}` = '{$value}'";
		}
		return "SET " . implode(', ', $retorno);
	}

	protected function _parseInsertValues()
	{
		if (!is_array($this->_values) || empty($this->_values)) {
			throw new \Exception("Insert values are missing", 1);			
		}

		$keys = implode(", ", array_keys($this->_values));
		$values = array();
		
		foreach ($this->_values as $key => $value) {
			if (null == $value) {
				$values[] = 'NULL';
				continue;
			}
			$values[] = is_numeric($value) ? $value : "'{$value}'";
		}

		$values = implode(", ", $values);

		return "({$keys}) VALUES ({$values})";
	}

	protected function _parseGroup()
	{
		if (is_null($this->_group)) {
			return '';
		}
		return " GROUP BY " . $this->_delimitarValue($this->_group);
	}

	protected function _parseOrder()
	{
		if (is_null($this->_order)) {
			return '';
		}
		return " ORDER BY " . $this->_delimitarValue($this->_order);
	}

	protected function _parseLimit()
	{
		if (is_null($this->_offset)) {
			return '';
		}

    	$limit = "LIMIT {$this->_offset}";
    	if ($this->_count > 0) {
    		$limit .= ", {$this->_count}";
		}

		return $limit;
	}

	public function __toString()
	{
		return $this->sql();
	}
}
