<?php 

	header('Content-Type: text/html; charset=utf-8');

	/**
	 * Documentacao da Classe Query
	 * @autor - wanderlei santana
	 */
	class Query
	{
	    # @var this object - informa se este objeto ja esta inicializado
	    private static $initialized = false;
	    
	    /**
	     * Metodo utilizado para obter uma instancia dinamicamente desta Classe
	     * Metodo Singleton empregado
	     * 
	     * @return void
	     */
	    public static function getInstance()
	    {
	    	if (self::$initialized) return;
	    	self::$initialized = true;
	    }

	    /**
	     * Método utilizado pra realizar a criação de um Select
	     * 
	     * @param  string - Campos a serem selecionados na tabela 
	     * @return QSet   - retorna objeto do tipo QSet
	     */
	    public static function select( $__campos__ = '*' ){
	        self::getInstance();
	        $__sql__ = "SELECT $__campos__ ";
	        return new QSet($__sql__) ;
	    }
		
		/**
		 * Metodo utilizado para realizar Updates em uma determinada 
		 * tabela do banco de dados
		 * 
		 * @param  string - Nome da Tabela
		 * @return QSet - Retorna um objeto do tipo QSet
		 */
		public static function update($__tabela__ = null){
	        self::getInstance();
	        $__sql__ = "UPDATE $__tabela__";
	        return new QSet($__sql__) ;
		}
	}

	class Teste
	{
		public function __construct(){}
	}

	$__subject__ = '
	class Query
	{
	    # @var this object - informa se este objeto ja esta inicializado
	    private static $initialized = false;
	    
	    # retorna instancia desta cclasse
	    public static function getInstance()
	    {
	    	if (self::$initialized) return;
	    	self::$initialized = true;
	    }

	    public static function select( $__campos__ = \'*\' ){
	        self::getInstance();
	        $__sql__ = "SELECT $__campos__ ";
	        return new QSet($__sql__) ;
	    }
		
		public static function update($__tabela__ = null){
	        self::getInstance();
	        $__sql__ = "UPDATE $__tabela__";
	        return new QSet($__sql__) ;
		}
	}

	class Teste
	{
		public function __construct(){}
	}
	';

#	$__pattern__ = '/class\s+(\w+)(.*)?\{/' ;
#	$__pattern__ = '/public\s/';

// $__subject__ = "abcdef ou então def public function ola{ inido def outra vez";
# $__pattern__ = '/def/i';
$__pattern__ = "'class\s(.*?){'si";
#$__pattern__ = "'public(.*?){'si";
# var_dump($__subject__);

	$_find_classes_ = array();

	if(preg_match_all($__pattern__, $__subject__, $_matches_)):
		list($_cls_) = $_matches_;
    	foreach ($_cls_ as $value)
    		$_find_classes_[] = trim(ltrim(rtrim(trim($value),'{'),'class')) ;
	endif;

// var_dump($_find_classes_);


foreach ($_find_classes_ as $__class__)
{
	echo "<hr>";
	// Obtem os comentarios de uma classe
	$rc = new ReflectionClass( $__class__ );
	$doc = preg_replace( "/ {2,}/", " ", $rc->getDocComment() );
	$doc = str_replace( "\t", "", $doc );
	echo '<pre>'.$doc.'</pre>';
	echo "<h3>Class : ".$__class__ . "</h3>";

	$methods = get_class_methods( $__class__ );
	// Print them out

	foreach ($methods as $__method__):
	    // echo "  $method <br>";
	    if(isset($method)) 
	    	$method = null;

	    $method = new ReflectionMethod($__class__, $__method__);
	    $doc = preg_replace( "/ {2,}/", " ", $method->getDocComment() );
	    $doc = str_replace( "\t", "", $doc );
		echo '<pre>'.$doc.'</pre>';
		echo $method->getStartLine() . ": " ;
		echo $method->getName();
		// var_dump($method->getParameters());
	    // echo $__method__ . "<br>";

	endforeach;
}


/*
$fp = fopen($file, 'r');
$class = $buffer = '';
$method = $buffer = '';
while(!feof($fp))
{
    $buffer .= fread($fp, 512);
    if (preg_match('/class\s+(\w+)(.*)?\{/', $buffer, $matches)) {
        $class = $matches[1];
    }
    if (preg_match_all('/function\s+(\w+)(.*)?\{/', $buffer, $match)) {
        $method = $match[1];
        //print_r($match);
    }
}


echo "class:".$class."<br />";
//print_r($method);
foreach($method as $key=>$val)
{
echo "method : ".$val."<br />";
}
*/
