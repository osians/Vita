<?php 

require_once 'sanitarizacao.class.php' ;
require_once 'validacao.class.php' ;

class InputUndefinedIndexKeyException extends Exception{}
class InputNotDefinedMethodException extends Exception{}

class Post {
	
	protected static $data = array();
	protected static $sanitize = null;
	protected static $validate = null;
	private static $initialized = false;

	public static function init()
	{
		if(self::$initialized) return;

		if(isset($_POST))
			self::$data = $_POST;
		$_POST = null;

		self::$sanitize = new Validacao\Sanitarizacao;
		self::$validate = new Validacao\Validacao;
		self::$initialized = true ; 
	}

	public static function get($key) {
		self::$initialized || self::init();
		return self::$sanitize->tornarSeguro( self::$data[$key] );
	}

	public static function setInput($_dados) {
		self::$data = $_dados; 
	}

    public static function __callStatic($name, $arguments)
    {
    	self::$initialized || self::init();

    	if(!isset(self::$data[$arguments[0]]))
    		throw new InputUndefinedIndexKeyException(
    			"Você está tentando obter o valor '{$arguments[0]}' que não esta definido.", 1);
    		
        if(method_exists(self::$sanitize,$name))
        	return self::$sanitize->$name( self::$data[$arguments[0]] );
    
        if(method_exists(self::$validate,$name))
        	return self::$validate->$name( self::$data[$arguments[0]] );

    	throw new InputNotDefinedMethodException(
    		"O Método '".$name."' não existe nas classes de Validação.", 1);

    }

	public static function decimal( $key = null, $precisao = null ) {
		self::$initialized || self::init();
		return self::$sanitize->decimal( self::$data[$key] );
	}

}

class Get extends Post 
{
	/**
	 * Sobrescrevendo o metodo init para
	 * inicializar $_GET. Os outros métodos
	 * devem continuar iguais.
	 **/
	public static function init() {
		if(self::$initialized) return;
		if(isset($_GET))
			self::$data = $_GET;
		$_GET = null;
		self::$sanitize = new Validacao\Sanitarizacao;
		self::$initialized = true ; 
	}
}
