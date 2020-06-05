<?php

namespace Osians\DataEntityManager;

class Entity implements EntityInterface
{
	/**
	 * id_*
	 * @var int
	 */
	protected $_id = null;

	protected $__entityManager = null;

	protected $__lazyLoad = array();

	/**
	 *    Nome da tabela da Entidade.
	 *    Mantenha null para usar o nome da
	 *    classe como nome da Tabela
	 *    @var String
	 */
	protected $__tableName = null;

    public function __construct(){}

	/**
	 *    @see EntityManager::setId()
	 */
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}

	/**
	 *    @see EntityManager::getId()
	 */
	public function getId()
	{
		return $this->_id;
	}

	public function setEntityManager(EntityManager $entityManager)
	{
		$this->__entityManager = $entityManager;
		return $this;
	}

	public function getEntityManager()
	{
		return $this->__entityManager;
	}

	/**
	 *    @see  EntityInteface::init()
	 *    @return void
	 */
	public function init(\StdClass $object)
	{
		foreach (get_object_vars($object) as $key => $value) {
			$property = $this->_snakeCaseToCamelCase($key);
			$this->{$property} = $value;
		}
	}

	/**
	 *    Dado o nome de uma Coluna do banco de dados converte esta
	 *    para uma propriedade CamelCase PSR-4
	 *    @param  String $key
	 *    @return String
	 */
	protected function _snakeCaseToCamelCase($text, $capitalizeFirstCharacter = false)
	{
		if ($text == $this->getPrimaryKeyName()) {
			return '_id';
		}

		if ($this->_isLazyLoadProperty(str_replace('id', '', $text))) {
			return str_replace('id', '', $text);
		}

		$text = str_replace('_', '', ucwords($text, '_'));
    	if (!$capitalizeFirstCharacter) {
        	$text = lcfirst($text);
    	}

    	return "_{$text}";
	}


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
	 *    Dado um ID qualquer carrega os dados do banco de dados
	 *    para esta Entity
	 *
	 *    @param  integer $id
	 *    @return Entity
	 */
	public function load($id)
	{
		$this->getEntityManager()->load($this, $id);
		return $this;
	}

	/**
	 *    @alias
	 */
	public function byId($id)
	{
		return $this->load($id);
	}

	/**
	 *    Call para metodos nao implementados da Entity
	 *
	 *    @param  String $method
	 *    @param  Array $argumentos
	 *
	 *    @return Entity
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
			return $this->_callGetMethod($this->getPropertyFromMethodName($method), $argumentos);
		}

		throw new \Exception("O método '{$method}' não existe na classe '" . get_class($this) . "'.");
	}

	/**
	 *    Verifica se o metodo chamado pelo cliente é um get*
	 *
	 *    @param  String $method
	 *    @return boolean
	 */
	protected function _isValidGetMethod($method)
	{
		return substr(strtolower($method), 0, 3) === 'get';
	}

	/**
	 *    Verifica se o metodo chamado pelo cliente é um set*
	 *
	 *    @param  String $method
	 *    @return boolean
	 */
	protected function _isValidSetMethod($method)
	{
		return substr(strtolower($method), 0, 3) === 'set';
	}

	/**
	 *    Simula uma chamada a um metodo set* na classe
	 *
	 *    @param  string $property
	 *    @param  Mixed $value
	 *
	 *    @return Entity
	 */
	protected function _callSetMethod($property, $value)
	{
		$this->_checkIfPropertyExists($property);

		$this->{$property} = $value;
		return $this;
	}

	/**
	 *    Simula chamada a um metodo get* da classe
	 *    @param  String $property
	 *    @param  String $argumentos - recebido na função __call
	 *    @return Mixed
	 */
	protected function _callGetMethod($property, $argumentos)
	{
		$this->_checkIfPropertyExists($property);
		
		return isset($this->{$property}) && $this->_isLazyLoadProperty($property) == false
			? $this->{$property}
			: $this->_callGetLazyLoadMethod($property, $argumentos);
	}

	/**
	 *    Verifica se a propriedade Existe dentro da classe
	 *    @param  String $property
	 *    @return void
	 */
	protected function _checkIfPropertyExists($property)
	{
		if (property_exists($this, $property) == false && 
			$this->_isLazyLoadProperty($property) == false) {
			throw new \Exception("A Propriedade '{$property}' não existe na classe '" . get_class($this) . "'.", 1);
		}
	}

	/**
	 *    Verifica se propriedade e' um indice do array LazyLoad
	 *
	 *    @param  String $property
	 *    @return boolean
	 */
	protected function _isLazyLoadProperty($property)
	{
		return array_key_exists($property, $this->__lazyLoad);
	}

	/**
	 *    Retorna o Valor setado em uma chave do LazyLoad
	 *    @param  String $key
	 *    @return String
	 */
	protected function _getLazyLoadValue($key)
	{
		return $this->__lazyLoad[$key];
	}

	/**
     *    Retorna o nome de uma Propriedade a partir do nome de um Metodo
     *
	 *    @param  String $method - Nome do Metodo
	 *    @return String
	 */
	protected function getPropertyFromMethodName($method)
	{
		return '_' . lcfirst(substr($method, 3));
	}

	/**
	 *    Chama o metodo get* para uma propriedade contida dentro
	 *    do LazyLoad da Entidade.
	 *
	 *    @param  String $property - Nome da Propriedade|key
	 *    @param  Array $argumentos - argumentos passados pelo Cliente
	 *
	 *    @return EntityInterface
	 */
	protected function _callGetLazyLoadMethod($property, $argumentos)
	{
		$value = $this->_getLazyLoadValue($property);
		
		$argumento = !empty($argumentos) 
			? $argumentos[0]
			: (isset($value['relation']) ? $value['relation'] : null);

		switch ($this->_checkRelationFromArgumento($argumento)) {
			case 'n-n':
				return $this->_loadNtoN($property);
				break;

			case '1-n':
				return $this->_loadOneToN($property);
				break;

			case '1-1':
				return $this->_loadOneToOne($property);
				break;

			default: break;
		}
	}

	/**
	 *    Verifica se o parametro passado pelo cliente esta no padrao aceito
	 *    @param  String $argumento
	 *    @param  String $default - n-1
	 *    @return String - 'n-n', '1-1' ou '1-n'
	 */
	protected function _checkRelationFromArgumento($argumento, $default = '1-1')
	{
		$validRelations = array('n-n', '1-1', '1-n');
		return isset($argumento) && in_array($argumento, $validRelations) 
			? $argumento 
			: $default;
	}

	/**
	 *    Carrega um relacionamento 1:1 entre duas entidades
	 *    @param  String $lazyLoadProperty - Nome da Key no array LazyLoad
	 *    @return EntityInterface
	 */
	protected function _loadOneToOne($lazyLoadProperty)
	{
		$lazyload = $this->_getLazyLoadValue($lazyLoadProperty);
var_dump($lazyload);die();
		$propertyExists = property_exists($this, $lazyLoadProperty);
		if ($propertyExists && is_object($this->{$lazyLoadProperty})) {
			return $this->{$lazyLoadProperty};
		}

		if ($propertyExists && !is_numeric($this->{$lazyLoadProperty})) {
			throw new Exception("Chave estrangeira não encontrada", 1);
		}

		$value = $this->_getLazyLoadValue($lazyLoadProperty);
		$entityClass = is_string($value) ? $value : $value['target'];

		$entity = new $entityClass();
var_dump($lazyLoadProperty, $this);die();
		$this->getEntityManager()->load($entity, $this->{$lazyLoadProperty});
		$this->{$lazyLoadProperty} = $entity;

		return $entity;
	}

	protected function _loadOneToN($lazyLoadProperty)
	{
		//var_dump($lazyLoadProperty);
		die("@todo - implementar carregamento LazyLoad 1-N");
	}

	protected function _loadNtoN($lazyLoadProperty)
	{
		die("@todo - implementar carregamento LazyLoad N-N");
	}


	/**
	 *    @see EntityInterface::getTableName()
	 *    @return String
	 */
	public function getTableName()
	{
		return null != $this->__tableName 
			? $this->__tableName 
			: strtolower(get_class($this)); 
	}

	/**
	 *    @see EntityInterface::getPrimaryKeyName()
	 *    @return String
	 */
	public function getPrimaryKeyName()
	{
		return "id_{$this->getTableName()}";
	}

	/**
	 *    Retorna Propriedades do Objeto Cliente em formato de Array
	 *    @return Array
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
	 *    Verifica se um nome qualquer segue o padrao de nomes de
	 *    propriedades de classes modelo. Ou seja: "_NomeDaPropriedade"
	 *
	 *    @param  String $property
	 *    @return boolean
	 */
	protected function _isValidModelPropertyName($property)
	{
		return $property[0] == '_' && $property[1] != '_';
	}

	public function save()
	{
		return $this->getEntityManager()->save($this);
	}

    /**
     *    Caso clone, elimina o ID na classe resultante
     */
    public function __clone()
    {
        $this->_id = null;
    }
}
