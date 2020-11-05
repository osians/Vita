<?php

namespace Osians\VeManager;

use Exception;

/**
 * Query Builder
 */
class QueryBuilder implements QueryBuilderInterface
{
    /**
     * @static Select statement
     */
    const SELECT = 1;

    /**
     * @static Delete statement
     */
    const DELETE = 2;

    /**
     * @static Update statement
     */
    const UPDATE = 3;

    /**
     * @static Insert statement
     */
    const INSERT = 4;

    /**
     * @var String - Select, Insert, Delete, Update
     */
    protected $_action = null;

    /**
     * Nome da tabela principal em que a consulta ocorre
     *
     * @var array | string
     */
    protected $_from = null;

    /**
     * @var null - Returned Fields
     */
    protected $_fields = array();

    /**
     * Tables Joined
     *
     * @var array
     */
    protected $_join = array();

    /**
     * Where Conditions
     *
     * @var array
     */
    protected $_where = array();

    /**
     * Used for Update
     *
     * @var array
     */
    protected $_set = array();

    /**
     * Group by
     *
     * @var String
     */
    protected $_group = null;

    /**
     * Order
     *
     * @var String
     */
    protected $_order = null;

    /**
     * @var integer
     */
    protected $_offset = null;

    /**
     * @var integer
     */
    protected $_count = 1000;

    /**
     * Keeps all tables that have been used in the Query
     *
     * @var array
     */
    protected $_usedTables = array();

    /**
     * When TRUE, this force to return Joined Table Primary Key in  the Query
     *
     * @var bool
     */
    protected $_returnJoinPK = true;

    /**
     * @var array
     */
    private $_values = array();

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Set if Joined Tables will be forced to return Primary Key
     *
     * @param bool $val
     *
     * @return $this
     */
    public function setReturnJoinPrimaryKey($val = true)
    {
        $this->_returnJoinPK = ($val === true);
        return $this;
    }
        
    public function getReturnJoinPrimaryKey()
    {
        return $this->_returnJoinPK;
    }
    
    /**
     * Init a Select Statement query
     *
     * @return QueryBuilder
     */
    public function select()
    {
        $this->_action = QueryBuilder::SELECT;
        return $this;
    }

    /**
     * Init a Delete Statement query
     *
     * @return QueryBuilder
     */
    public function delete()
    {
        $this->_action = QueryBuilder::DELETE;
        return $this;
    }

    /**
     * Init a Update Statement query
     *
     * @param string $tableName
     *
     * @return QueryBuilder
     */
    public function update($tableName)
    {
        $this->_from = $tableName;
        $this->_action = QueryBuilder::UPDATE;
        return $this;
    }

    /**
     * Init a Insert Statement query
     *
     * @return QueryBuilder
     */
    public function insert()
    {
        $this->_action = QueryBuilder::INSERT;
        return $this;
    }

    /**
     * Seta valores para um Update
     *
     * @param Mixed $set - Ex: set('campo = ?', 'value')
     *
     * @return QueryBuilder
     */
    public function set()
    {
        $this->_set[] = func_get_args();
        return $this;
    }

    /**
     * Values used for insert statemant
     *
     * @param  array $values
     *
     * @return QueryBuilder
     */
    public function values($values)
    {
        $this->_values = $values;
        return $this;
    }

    /**
     * Registra os campos/colunas que serao retornados
     * da consulta SQL
     *
     * @param array  $fields - campos a retornar, onde:
     *               null: indica tudo
     *               []  : indica nada
     *               ['fieldname']: indica um campo a retornar
     *
     * @param String $prefix - prefixo, antes do nome da coluna.
     *
     * @param String $tablename - nome da tabela
     *
     * @return QueryBuilder
     */
    protected function _addFields($fields = null, $prefix = null, $tablename = null)
    {
        // array vazio: indica que nao quer retornar campos dessa tabela
        if (is_array($fields) && empty($fields)) {
            return $this;
        }

        // default: retorna todos os campos da tabela
        if (null == $fields) {
            $this->_fields[] = [
                'value' => '*',
                'prefix'=> $prefix,
                'owner' => $tablename,
                'alias' => null
            ];

            return $this;
        }

        // retorna campos especificados no array
        foreach ($fields as $key => $value) {
            $this->_fields[] = [
                'value' => $value,
                'prefix'=> $prefix,
                'owner' => $tablename,
                'alias' => ((is_numeric($key) == false) ? $key : null)
            ];
        }

        return $this;
    }

    /**
     * Adicionar Delimitadores (`) a valores
     *
     * @param String $value
     * @param String $prefix
     *
     * @return String
     */
    private function _delimitarValue($value, $prefix = null)
    {
        if (null != $prefix && strpos($value, "{$prefix}.") !== 0) {
            return "`{$prefix}`.`{$value}`";
        }

        if ((null != $prefix && strpos($value, $prefix) === 0) ||
            (null == $prefix && strpos($value, ".") > 0) ||
            (null != $prefix && strpos($value, $prefix) > 0)) {

            list($prefix, $field) = explode(".", $value);
            if (strpos($field, " ") === false) {
                return "`{$prefix}`.`{$field}`";
            }

            list($campo, $sufixo) = explode(" ", $field);
            return "`{$prefix}`.`{$campo}` {$sufixo}";
        }

        return $value;
    }

    /**
     * Tabela em que as alterações acontecerao
     *
     * @param string|array $table - Tablename
     * @param array|null $fields - []: indica nenhum campo,
     *                             null: indica todos os campos por default
     *
     * @return QueryBuilder
     */
    public function from($table, $fields = null)
    {
        $this->_from = $table;

        $prefix = is_array($table) ? array_keys($table)[0] : null;
        $tablename = is_array($table) ? array_shift($table) : $table;

        $this->_addFields($fields, $prefix, $tablename);
        $this->_addUsedTables($tablename);

        return $this;
    }

    /**
     * @alias para from
     *
     * @param string|array $table
     *
     * @return QueryBuilder
     */
    public function into($table)
    {
        return $this->from($table);
    }

    /**
     * Adiciona um Inner Join a Consulta
     *
     * @param  array  $table
     * @param  String $on
     * @param  array  $fields
     *
     * @return QueryBuilder
     */
    public function innerJoin($table, $on, $fields = array())
    {
        $alias = is_array($table) ? array_keys($table)[0] : null;
        if ($alias === 0) {
            $alias = null;
        }

        $tablename = is_array($table) ? array_shift($table) : $table;

        //    forca o retorna da chave primaria das tabelas agregadas via JOIN
        //    @todo - precisa testar com cuidado essa opcao de "*"
        if (!in_array("id_{$tablename}", $fields) && !in_array('*', $fields) && $this->getReturnJoinPrimaryKey() == true) {
            $fields[] = "id_{$tablename}";
        }

        $this->_addFields($fields, $alias, $tablename);
        $this->_join[] = array(
            'type'  => 'INNER',
            'table' => $tablename,
            'alias' => $alias,
            'on'    => $on
        );

        $this->_addUsedTables($tablename);

        return $this;
    }

    /**
     * Add a Left Join to the Query
     *
     * @param  String | array $table
     * @param  String $on
     * @param  array $fields
     *
     * @return QueryBuilder
     */
    public function leftJoin($table, $on, $fields = array())
    {
        $alias = is_array($table) && array_keys($table)[0] != 0 ? array_keys($table)[0] : null;
        $tablename = is_array($table) ? array_shift($table) : $table;

        //    forca o retorna da chave primaria das tabelas
        //    agregadas via JOIN
        if (!in_array("id_{$table}", $fields)) {
            $fields[] = "id_{$table}";
        }

        $this->_addFields($fields, $alias[0], $tablename);

        $this->_join[] = array(
            'type' => 'LEFT',
            'table' => $tablename,
            'alias' => $alias,
            'on' => $on
        );

        $this->_addUsedTables($tablename);

        return $this;
    }

    public function where()
    {
        $this->_where[] = func_get_args();
        return $this;
    }

    /**
     * How to group the query
     * Ex: ->group('date');
     *
     * @param  String $group
     *
     * @return QueryBuilder
     */
    public function group($group)
    {
        $this->_group = $group;
        return $this;
    }

    /**
     * How the query will be ordered
     * Ex: ->order('date ASC');
     *
     * @param  String $order
     *
     * @return QueryBuilder
     */
    public function order($order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Query Limit
     * Ex: ->limit(1);
     *
     * @param  Int $limit
     *
     * @return QueryBuilder
     */
    public function limit($limit)
    {
        $this->offset(0);
        return $this->count($limit);
    }

    /**
     * Offset
     *
     * @param  Int $offset
     *
     * @return QueryBuilder
     */
    public function offset($offset = 0)
    {
        $this->_offset = $offset;
        return $this;
    }

    /**
     * Number of records to return
     *
     * @param  Int $count
     *
     * @return QueryBuilder
     */
    public function count($count)
    {
        $this->_count = $count;
        return $this;
    }

    /**
     * @see  EntityQueryInterface::getTableName()
     * @return String
     */
    public function getTableName()
    {
        return $this->_from;
    }

    private function _getRelationAliasTableFromFields()
    {
        $relation = array();

        foreach ($this->_fields as $field) {
            if ($field['prefix'] != null && $field['owner'] != null) {
                $relation[$field['prefix']] = $field['owner'];
            }
        }

        if (is_array($this->_from)) {
            $prefix = array_keys($this->_from)[0];
            $table = array_values($this->_from)[0];
            $relation[$prefix] = $table;
        }

        return $relation;
    }

    /**
     * Returns an Array informing which table each query field belongs to
     *
     * @return array
     */
    public function getFieldMap()
    {
        $relation = $this->_getRelationAliasTableFromFields();

        // $sql = str_replace('`', '', $this->sql());
        // $sql = preg_replace('!\s+!', ' ', $sql);
        // $sql = str_replace(array("\n", "\t", "\r"), '', $sql);

        foreach($this->_fields as &$field)
        {
            if ($field['owner'] !== null) {
                continue;
            }

            if ($field['prefix'] !== null && isset($relation[$field['prefix']])) {
                $field['owner'] = $relation[$field['prefix']];
                continue;
            }
        }

        return $this->_fields;
    }


    /**
     * Tenta identificar a qual tabela pertence um determinado campo.
     *
     * @param  String $column - nome do Campo/Coluna
     *
     * @return String - Nome da Tabela
     */
    public function getFieldOwner($column = null)
    {
        $return = null;

        foreach ($this->getFieldMap() as $field) {
            if ($field['value'] === $column) {
                $return = $field['owner'];
                break;
            }
        }

        if (null === $return/* && empty($this->_join)*/) {
            $return = is_array($this->_from) ? reset($this->_from) : $this->_from;
        }

        return $return;
    }

    /**
     * Return Raw SQL
     *
     * @return String
     * @throws Exception
     */
    public function sql()
    {
        $sql = '';

        switch ($this->_action) {
            case QueryBuilder::SELECT:

                $sql = "SELECT {$this->_parseFields()}
                                FROM {$this->_parseFrom()}
                                {$this->_parseJoins()}
                                {$this->_parseWhere()}
                                {$this->_parseGroup()}
                                {$this->_parseOrder()}
                                {$this->_parseLimit()}";
                break;

            case QueryBuilder::DELETE:

                $sql = "DELETE FROM {$this->_parseFrom()}
                        {$this->_parseJoins()}
                        {$this->_parseWhere()}
                        {$this->_parseLimit()}";
                break;

            case QueryBuilder::UPDATE:

                $sql = "UPDATE {$this->_parseFrom()}
                        {$this->_parseSet()}
                        {$this->_parseWhere()}
                        {$this->_parseLimit()}";
                break;

            case QueryBuilder::INSERT:

                $sql = "INSERT INTO {$this->_parseFrom()}
                        {$this->_parseInsertValues()}";
                break;

            default: break;
        }

        $sql = preg_replace('!\s+!', ' ', $sql);
        return str_replace(array("\n", "\t", "\r"), '', $sql);
    }

    /**
     * Return Raw SQL
     *
     * @return String
     * @throws Exception
     */
    public function getSql()
    {
        return $this->sql();
    }

    /**
     * Parse info about the main Table in the Query
     *
     * @return String
     * @throws Exception
     */
    protected function _parseFrom()
    {
        if (is_null($this->_from)) {
            throw new Exception("'FROM' property is missing");
        }

        if (is_array($this->_from)) {
            $alias = array_keys($this->_from);
            $table = array_values($this->_from);

            //$from = ($table[0] instanceof EntityQueryInterface)
            $from = ($table[0] instanceof QueryBuilderInterface)
                ? "({$table[0]->sql()})" : "`{$table[0]}`";

            return "{$from} AS `{$alias[0]}`";
        }

        return "`{$this->_from}`";
    }


    /**
     * Processa todos os campos que serão retornados na Consulta
     *
     * @return String - Colunas das tabelas a serem retornadas
     */
    protected function _parseFields()
    {
        if (empty($this->_fields)) {
            return "*";
        }

        $campos = array();

        foreach ($this->_fields as $field) {

            $tmp = isset($field['prefix'])
                ? "`{$field['prefix']}`.`{$field['value']}`"
                : "`{$field['owner']}`.`{$field['value']}`";

            if (isset($field['alias'])) {
                $tmp .= " AS `{$field['alias']}`";
            }

            $campos[] = $tmp;
        }

        return implode(", ", $campos);
    }

    protected function _parseJoins()
    {
        if (empty($this->_join)) {
            return '';
        }

        $statement = array();
        foreach ($this->_join as $join) {
            $alias = (!is_null($join['alias'])) ? "AS `{$join['alias']}`" : '';
            $statement[] = "{$join['type']} JOIN `{$join['table']}` {$alias} ON {$join['on']}";
        }

        return implode(' ', $statement);
    }

    /**
     * Realiza Parse das condicoes do Where
     *
     * @return string
     */
    protected function _parseWhere()
    {
        if (empty($this->_where)) {
            return '';
        }

        $statement = [];

        foreach ($this->_where as $where)
        {
            if (strpos($where[0], '?') === false) {
                $statement[] = "({$where[0]})";
                continue;
            }

            $stmt = array_shift($where);
            foreach ($where as $replace) {
                $replace = is_numeric($replace) ? $replace : "'{$replace}'";
                $stmt = preg_replace('/\?/i', $replace, $stmt, 1);
            }

            $statement[] = "({$stmt})";
        }

        return 'WHERE ' . implode(' AND ', $statement);
    }

    /**
     * Realiza Parse do array SET, normalmente
     * usado em UPDATEs;
     *
     * @return String
     */
    protected function _parseSet()
    {
        if (empty($this->_set)) {
            return '';
        }

        $statement = [];

        foreach ($this->_set as $data) {
            if (strpos($data[0], '?') === false) {
                $statement[] = "({$data[0]})";
                continue;
            }

            $key = substr($data[0], 0, strpos($data[0], "=") - 1);
            $statement[] = "`{$key}` = '{$data[1]}'";
        }

        return "SET " . implode(', ', $statement);
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function _parseInsertValues()
    {
        if (!is_array($this->_values) || empty($this->_values)) {
            throw new Exception("Insert values are missing", 1);
        }

        $keys = "`" . implode("`, `", array_keys($this->_values)) . "`";
        $values = array();

        foreach ($this->_values as $key => $value) {
            if (null === $value) {
                $values[] = 'NULL';
                continue;
            }
            $values[] = "'{$value}'";
        }

        $values = implode(", ", $values);

        return "({$keys}) VALUES ({$values});";
    }


    /**
     * Processa Informações de Agrupamento para
     * gerar SQL
     *
     * @return String
     */
    protected function _parseGroup()
    {
        if (is_null($this->_group)) {
            return '';
        }
        return " GROUP BY " . $this->_delimitarValue($this->_group);
    }


    /**
     * Processa Informações de Ordenação para
     * gerar Query SQL
     *
     * @return String
     */
    protected function _parseOrder()
    {
        if (is_null($this->_order)) {
            return '';
        }
        return " ORDER BY " . $this->_delimitarValue($this->_order);
    }


    /**
     * Processa Informações de Limit para
     * gerar Query SQL
     *
     * @return String
     */
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

    /**
     * Add used Table to list
     *
     * @param String $table
     *
     * @return  QueryBuilder
     */
    protected function _addUsedTables($table)
    {
        $this->_usedTables[] = $table;
        return $this;
    }

    /**
     * return list of used tables in this query
     *
     * @return array
     */
    public function getUsedTables()
    {
        return $this->_usedTables;
    }

    /**
     * Caso chame objeto como String, retorna a SQL
     * compilada.
     *
     * @return String
     * @throws Exception
     */
    public function __toString()
    {
        return $this->sql();
    }
}
