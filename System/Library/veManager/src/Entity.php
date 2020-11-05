<?php

namespace Osians\VeManager;

use Exception;
use StdClass;

abstract class Entity implements EntityInterface
{
    /**
     * Nome da tabela da Entidade.
     * Mantenha nha null para usar o nome da
     * classe como nome da Tabela
     *
     * @var String
     */
    protected $__tableName = null;

    /**
     * Keeps Model Changed Fields
     *
     * @var array
     */
    protected $_changedFields = array();
    
    /**
     * Guarda Query que deu origem a essa entidade
     *
     * @var QueryBuilderInterface
     */
    private $__query = null;
    
    /**
     * Entity Manager
     *
     * @var VeManager
     */
    protected $__em = null;

    /**
     * Entity Unique Identification
     *
     * @var integer
     */
    protected $_id = null;

    /**
     * Construct
     */
    public function __construct()
    {
    }

    /**
     * Set Entity ID
     *
     * @param Int $id
     *
     * @return Entity
     */
    public function setId($id)
    {
        $idProperty = $this->_snakeCaseToCamelCase($this->getPrimaryKeyName());
        $this->$idProperty = $id;
        return $this;
    }

    /**
     * Get Entity ID
     *
     * @return Int
     */
    public function getId()
    {
        $idProperty = $this->_snakeCaseToCamelCase($this->getPrimaryKeyName());
        return $this->$idProperty;
    }

    /**
     * Initialize Entity
     *
     * @param stdClass $object
     *
     * @return Entity
     */
    public function init(StdClass $object)
    {
        foreach (get_object_vars($object) as $key => $value) {
            $property = $this->_snakeCaseToCamelCase($key);
            $this->{$property} = $value;
        }
        return $this;
    }

    /**
     * Converts a given snake case text format into Camel case
     *
     * @param  String $text
     *
     * @return String
     */
    protected function _snakeCaseToCamelCase($text)
    {
        return '_' . lcfirst(
            str_replace('_', '', ucwords($text, '_'))
        );
    }

    /**
     * Converts a given Camel case text format into Snake case
     *
     * @param  String $text
     *
     * @return String
     */
    protected function _camelCaseToSnakeCase($text = '')
    {
        if ($text == '_id') {
            return $this->getPrimaryKeyName();
        }

        return ltrim(strtolower(preg_replace(
            ["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"],
            ["_$1", "_$1_$2"], lcfirst($text))), '_');
    }

    /**
     * Call para metodos nao implementados da Entity
     *
     * @param  string $method
     * @param  array $argumentos
     *
     * @return Entity
     * 
     * @throws Exception
     */
    public function __call($method, $argumentos)
    {
        if ($this->_isValidSetMethod($method)) {
            $this->_callSetMethod(
                $this->getPropertyFromMethodName($method),
                $argumentos[0]
            );
            return $this;
        }

        if ($this->_isValidGetMethod($method)) {
            return $this->_callGetMethod(
                $this->getPropertyFromMethodName($method)
            );
        }

        throw new Exception(
            "Method '{$method}' does not exist in the class '".get_class($this)."'."
        );
    }

    /**
     * Verifica se o metodo chamado pelo cliente é um get*
     *
     * @param  String $method
     *
     * @return boolean
     */
    protected function _isValidGetMethod($method)
    {
        return substr(strtolower($method), 0, 3) === 'get';
    }

    /**
     * Verifica se o metodo chamado pelo cliente é um set*
     *
     * @param  String $method
     *
     * @return boolean
     */
    protected function _isValidSetMethod($method)
    {
        return substr(strtolower($method), 0, 3) === 'set';
    }

    /**
     * Call Set Method
     *
     * @param string $property
     * @param mixed $value
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function _callSetMethod($property, $value)
    {
        if (property_exists($this, $property) == false && $value instanceof EntityInterface) {
            $prop = $this->_snakeCaseToCamelCase("id_{$property}");
            if (property_exists($this, $prop) == true) {
                $this->{$prop} = $value;
                return $this;
            }
        }

        $this->_setChangedProperty($property, $this->{$property}, $value);
        $this->_checkIfPropertyExists($property);
        $this->{$property} = $value;
        return $this;
    }

    /**
     * Call Get Method
     *
     * @param string $property
     * @return mixed
     *
     * @throws Exception
     */
    protected function _callGetMethod($property)
    {
        // is LazyLoad?
        if (property_exists($this, $property) === false) {
            $prop = $this->_snakeCaseToCamelCase("id_{$property}");
            if (property_exists($this, $prop) == true && $this->{$prop} instanceof EntityInterface) {
                return $this->{$prop};
            }
             
            if (property_exists($this, $prop) == true && is_numeric($this->{$prop})) {
                $this->{$prop} = $this->getEntityManager()
                     ->get(ltrim($property, '_'), $this->{$prop});
                return $this->{$prop};
            }
        }

        // is a reference to a table in which the model id resides
        if (property_exists($this, $property) == false) {
            $tabname = $this->_camelCaseToSnakeCase($property);
            $tab = $this->getEntityManager()->getTableDesc($tabname);

            if ($tab !== false && isset($tab[$this->getPrimaryKeyName()])) {
                return $this->getEntityManager()->getBy(
                        $tabname, $this->getPrimaryKeyName(), $this->getId());
            }
        }
        
        $this->_checkIfPropertyExists($property);
        return $this->{$property};
    }
    
    /**
     * Verify if property exists in this class
     *
     * @param string $property
     *
     * @throws Exception
     */
    protected function _checkIfPropertyExists($property)
    {
        if (property_exists($this, $property) == false) {
            throw new Exception(
                "Property '{$property}' does not exist in class '".get_class($this)."'"
            );
        }
    }
    
    /**
     * Get Property Name from Method Name
     *
     * @param string $method
     *
     * @return string
     */
    protected function getPropertyFromMethodName($method)
    {
        return '_' . lcfirst(substr($method, 3));
    }
    
    /**
     * Set table Name
     *
     * @param string $tablename
     *
     * @return $this
     */
    public function setTablename($tablename)
    {
        $this->__tableName = $tablename;
        return $this;
    }
    
    /**
     * @see EntityInterface::getTableName()
     * @return String
     */
    public function getTableName()
    {
        return null != $this->__tableName 
            ? $this->__tableName 
            : strtolower(get_class($this)); 
    }

    /**
     * @see EntityInterface::getPrimaryKeyName()
     * @return String
     */
    public function getPrimaryKeyName()
    {
        return "id_{$this->getTableName()}";
    }

    /**
     * Verifica se um nome qualquer segue o padrao de nomes de
     * propriedades de classes modelo. Ou seja: "_NomeDaPropriedade"
     *
     * @param  String $property
     *
     * @return boolean
     */
    protected function _isValidModelPropertyName($property)
    {
        return $property[0] == '_' && $property[1] != '_';
    }

    /**
     * Retorna Propriedades do Objeto Cliente em formato de Array
     *
     * @return array
     */
    public function toArray()
    {
        $retorno = array();

        foreach (get_object_vars($this) as $property => $value) {
            if ($this->_isValidModelPropertyName($property)) {
                $key = $this->_camelCaseToSnakeCase($property);
                $retorno[$key] = $value;
            }
        }

        return $retorno;
    }

    /**
     * Quando uma propriedade do objeto e' alterada,
     * guarda o nome da propriedade para facilitar
     * obter os campos alterados na hora do persist.
     *
     * @param String $property - Model Class Property
     * @param String $before - Value Before Change
     * @param String $after - New Value
     *
     * @return Entity
     */
    protected function _setChangedProperty($property, $before, $after)
    {
        if (isset($this->_changedFields[$property])) {
            return $this;
        }
        
        $key = $this->_camelCaseToSnakeCase(substr($property, 1, strlen($property)));
        $owner = $this->_getOwner($key);

        $ownerId = $this->_snakeCaseToCamelCase("id_{$owner}");
        if (!property_exists($this, $ownerId)) {
            $this->$ownerId = null;
        }

        $this->_changedFields[$key] = array(
            'from' => $before,
            'to' => $after,
            'owner' => $owner,
            'id' => $this->$ownerId,
            'pk' => "id_{$owner}"
        );
       
        return $this;
    }
    
    /**
     * Returns Array with changed Properties
     *
     * @return array of Array - [from, to, owner, id, pk]
     */
    public function getChangedProperty()
    {
        return $this->_changedFields;
    }

    /**
     * Get the name of the Table that owner this field
     *
     * @param string $field
     *
     * @return string
     */
    private function _getOwner($field)
    {
        return null !== $this->getQueryBuilder()
            ? $this->getQueryBuilder()->getFieldOwner($field)
            : $this->getTableName();
    }
    
    /**
     * keeps the QueryBuilder for later use
     *
     * @param  QueryBuilderInterface $query
     *
     * @return  Entity
     */
    public function setQueryBuilder(QueryBuilderInterface $query)
    {
        $this->__query = $query;
        return $this;
    }

    /**
     * Returns QueryBuilder
     *
     * @return QueryBuilderInterface
     */
    public function getQueryBuilder()
    {
        return $this->__query;
    }
    
    /**
     * Set Entity Manager
     *
     * @param VeManager $em
     *
     * @return Entity
     */
    public function setEntityManager(VeManager $em)
    {
        $this->__em = $em;
        return $this;
    }
    
    /**
     * Get Entity Manager
     *
     * @return VeManager
     */
    public function getEntityManager()
    {
        return $this->__em;
    }
    
    /**
     *    Caso clone, elimina o ID na classe resultante
     */
    public function __clone()
    {
        $this->_id = null;
    }
}
