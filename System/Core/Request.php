<?php

namespace Vita\Core;

/**
 *
 * Classe simples, responsavel por tratar dados advindos de formularios
 * 
 * @todo - implementar novos mecanismos de filtro e segurancao de dados
 * @todo - implementar mecanismo para permitir posts com tags html
 *
 * @dependencias - SYS, SYS_Table, SYS_Validate
 **/
class Request implements RequestInterface
{
	/**
	 * Guarda dados de post
	 * @var array
	 */
	private $_post;

	/**
	 * Is Post Submitted
	 * @var boolean
	 */
	protected $_posted = false;

	/**
	 *    Construct
	 */
	public function __construct()
	{
	}

	/**
	 *    Recebe o $_POST do sistema, e trata-o 
	 *    antes que possa ser utilizado no sistema
	 *
	 *    @return void
	 **/
    public function init()
	{
		$this->_loadPost();
		$this->_loadFiles();
		$this->_setAditionalData();
    }

    protected function _loadPost()
    {
		if(!$_POST) {
			return;
		}

		foreach ($_POST as $k => $v) {
			$this->set($k, $v);
		}

		return $this;
    }

	/**
	 *    Set a new Form Value
	 *
	 *	  @param String $key
	 *	  @param Mixed $value
	 *	  @param Boolean $makeSafe - When true apply xss filter and mysql_real_scape_string
	 *
	 *    @return Request
	 *
	 *    @throws none
	 *
	 *    @todo - criar melhor mecanismo de validacao e filtro de dados
	 **/
	public function set($key, $value)
	{
		if (empty($key)) {
			return;
		}

		$key = $this->_makeSafe($key);
		$value = $this->_makeSafe($value);

		$this->_post[$key] = $value;

		return $this;
	}

    /**
     * Obtem um valor postado num formulario, dado o seu Name|Key
     * uso : Post::get( 'cnpj' );
     *
     * @param $key
     * @param null $default
     * @return mixed | null(caso nao encontrado)
     */
	public function get($key, $default = null)
	{
		return (isset($this->_post[$key])) ? $this->_post[$key] : $default;
	}

    protected function _loadFiles()
    {
		if (isset($_FILES)) {
			foreach ($_FILES as $key => $value) {
				if (empty($value["name"])) {
					continue;
				}
				$this->_post[$key] = $value;
			}
		}

		return $this;
    }

    protected function _setAditionalData()
    {
        # setando outras infos importantes ao form
        $this->setPosted(true);
        $this->set('action', ''); //&$_GET['request']
        $this->set('method', isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null);

        # HTTP_REFERE nao e' garantido ser enviado pelo cliente
        if (isset($_SERVER['HTTP_REFERER'])) {
        	$this->set('caller', $_SERVER['HTTP_REFERER']);
        }

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setPosted($value)
    {
    	$this->_posted = $value;
    	return $this;
    }

    /**
     * Check if a POST has been received
     * @return boolean
     */
    public function posted()
    {
    	return $this->_posted;
    }

    /**
     *    Try to make a String Safe to use
     *
     *    @param  Mixed $value - String or Array
     *
     *    @return Mixed
     */
    protected function _makeSafe($value)
    {
    	if (is_array($value)) {
    		$return = array();
    		foreach ($value as $key => $val) {
    			$return[$key] = $this->_makeSafe($val);
    		}
    		return $return;
    	}

        $value = str_replace(
            array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a"),
            array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z"),
            trim($value));

        return $this->_xssClean($value);

    }

	/*
	* XSS filter 
	*
	* This was built from numerous sources
	* (thanks all, sorry I didn't track to credit you)
	* 
	* It was tested against *most* exploits here: http://ha.ckers.org/xss.html
	* WARNING: Some weren't tested!!!
	* Those include the Actionscript and SSI samples, or any newer than Jan 2011
	*
	*
	* TO-DO: compare to SymphonyCMS filter:
	* https://github.com/symphonycms/xssfilter/blob/master/extension.driver.php
	* (Symphony's is probably faster than my hack)
	*/
	protected function _xssClean($data)
	{
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        return $data;
	}

	public function remove($key)
	{
		if (isset($this->_post[$key])) {
			unset($this->_post[$key]);
		}
		return $this;
	}

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

	public function dump()
	{
		return $this->_post;
	}
}
