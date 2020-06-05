<?php

namespace Osians\DataEntityManager;

class VirtualEntity
{
	public function __construct() {}

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

	protected function _snakeCaseToCamelCase($text)
	{
		$text = lcfirst(str_replace('_', '', ucwords($text, '_')));
    	return "_{$text}";
	}

	public function __call($method, $argumentos)
	{
		if ($this->_isValidSetMethod($method)) {
			$this->_callSetMethod($this->getPropertyFromMethodName($method), $argumentos[0]);
			return $this;
		}

		if ($this->_isValidGetMethod($method)) {
			return $this->_callGetMethod($this->getPropertyFromMethodName($method), $argumentos);
		}

		throw new \Exception("O método '{$method}' não existe na classe '" . get_class($this) . "'.");
	}

	protected function _isValidGetMethod($method)
	{
		return substr(strtolower($method), 0, 3) === 'get';
	}

	protected function _isValidSetMethod($method)
	{
		return substr(strtolower($method), 0, 3) === 'set';
	}

	protected function getPropertyFromMethodName($method)
	{
		return '_' . lcfirst(substr($method, 3));
	}

	protected function _callSetMethod($property, $value)
	{
		$this->_checkIfPropertyExists($property);
		$this->{$property} = $value;
		return $this;
	}

	protected function _callGetMethod($property, $argumentos)
	{
		$this->_checkIfPropertyExists($property);
		return $this->{$property};
	}

	protected function _checkIfPropertyExists($property)
	{
		if (property_exists($this, $property) == false) {
			throw new \Exception(
				"Property '{$property}' does not exist at '" . get_class($this) . "'"
			);
		}
	}
}
