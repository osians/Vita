<?php

namespace Vita\Core\Router;

use \Vita\Core\Router\RouterStatus;

class Router
{
    /**
     *    Nome do Arquivo Controller encontrado pelo Objeto Router
     *    @var String
     */
    protected $_controller = null;

    /**
     *    Metodo que sera chamado pelo Objeto Router
     *    @var String
     */
    protected $_action = null;
    protected $_folders = "";
    protected $_controllerFolder = null;
    protected $_params = array();
    protected $_baseUrl = null;

    /**
     *    Diretorio Identificado como contendo o Controller
     *    requisitado.
     *    @var String
     */
    protected $_directory = null;

    /**
     *    Guarda o status da requisicao
     *    @var integer - RouterStatus
     */
    protected $_dispacher = 0;

    /**
     *    Construct
     *
     *    @param String $controllerFolder - Pasta onde se encontram os Controllers
     *    @param String $request - Algo como $_GET['request']
     */
    public function __construct($controllerFolder, $request)
    {
        $this->setBaseUrl();
        $this->setRequest($request);
        $this->setControllerFolder($controllerFolder);
        $this->_parseRequest();
    }

    /**
     *    Seta qual o caminho para a pasta com os Controllers
     *    @param string $folder
     *    @return Router
     */
    public function setControllerFolder($folder)
    {
        $this->_controllerFolder = $folder;
        return $this;
    }

    /**
     *    Retorna Pasta indicada pelo Usuario para guardar os
     *    Controllers.
     *    @return String
     */
    public function getControllerFolder()
    {
        return $this->_controllerFolder;
    }

    public function setInput(\Vita\Core\Request $input)
    {
        $this->_input = $input;
        return $this;
    }

    public function getInput()
    {
        return $this->_input;
    }

    public function setResponse(\Vita\Core\Response $response)
    {
        $this->_response = $response;
        return $this;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function setRenderer(\Vita\Core\Renderer $renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }

    public function getRenderer()
    {
        return $this->_renderer;
    }

    /**
     *    Seta no Objeto qual o endereço da URL Base do sistema
     *    algo como: http://localhost.com
     *    @return Router
     */
    public function setBaseUrl()
    {
        $this->_baseUrl = sprintf(
            "%s://%s/",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME']
        );

        return $this;
    }

    /**
     *    Retorna a URL Base em que o sistema foi chamado
     *    @return String
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     *    Seta a String Solicitada. Normalmente $_GET['request']
     *    @param String $request
     *    @return Router
     */
    public function setRequest($request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     *    Retorna a URL solicitada e que esta sendo objeto de analise
     *    por essa classe.
     *    @return String
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     *    Metodo responsavel por fazer o Parse da URL
     *    recebida por este objeto.
     *    @return Route
     */
    protected function _parseRequest()
    {
        if (null == $this->getRequest()) {
            return $this;
        }

        $arrRequest = explode('/', trim($this->getRequest(), '/'));
        $this->setDirectory($this->getDirectoryFromRequestUrl($arrRequest));
        $request = $this->_parseRequestToArray($arrRequest);

        $this->setController($request['controller']);
        $this->setAction($request['action']);
        $this->setParams($request['params']);

        //$this->_requestUri = $this->getBaseUrl() . $this->getRequest() . "/";
        $this->_setDispacher();
        return $this;
    }

    protected function _parseRequestToArray($request)
    {
        $retorno = array();

        $retorno['controller'] = $request[0];
        array_shift($request);
        $retorno['action'] = isset($request[0]) ? $request[0] : 'index';
        array_shift($request);
        $retorno['params'] = count($request) > 0 ? $request : null;

        return $retorno;
    }

    /**
     *    Detecta o Diretorio em que o Controller solicitado via URL
     *    se encontra. Retorna caminho completo do Diretorio.
     *    @return String
     */
    public function getDirectoryFromRequestUrl(&$arrRequest)
    {
        $directory = $this->getControllerFolder();

        foreach ($arrRequest as $key => $folder) {
            $tmp = $directory . ucfirst($folder) . DIRECTORY_SEPARATOR;
            if (is_dir($tmp)) {
                $directory = $tmp;
                continue;
            }
            break;
        }

        $arrRequest = array_slice($arrRequest, $key, sizeof($arrRequest));
        return $directory;
    }

    public function setController($controller, $default = 'Index')
    {
        $this->_controller = !empty($controller) ? ucfirst($controller) : $default;
        return $this;
    }

    public function getController()
    { 
        return $this->_controller;
    }
    
    public function setAction($action, $default = 'index')
    {
        $this->_action = !empty($action) ? $action : $default;
        return $this;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function setParams($params)
    {
        if (null == $params) {
            $params = array();
        }

        $this->_params = $params;
        return $this;
    }

    /**
     *    Seta o Status final do Nosso Roteamento
     *    @return void
     */
    protected function _setDispacher()
    {
        $filename = $this->getFilePath();
        
        if (!file_exists($filename)) {
            $this->_dispacher = RouterStatus::CONTROLLER_NOT_FOUND;
            return;
        }

        require_once $filename;
        if (!class_exists($this->getController())) {
            $this->_dispacher = RouterStatus::CLASS_NOT_FOUNT;
            return;
        }

        if (!is_callable(array($this->getController(), $this->getAction()))) {
            $this->_dispacher = RouterStatus::METHOD_NOT_FOUND;
            return;
        }

        $this->_dispacher = RouterStatus::FOUND;
    }

    /**
     *    Uma vez chamado, esse metodo realiza o roteamento
     *    e chama o Controller e o action requerido.
     *    @return void
     */
    public function rotear()
    {
        $file = $this->getFilePath();

        if (!is_readable($file)) {
            $this->_showErrorFileNotReadable();
            return;
        }

        require_once $file;
        $class = $this->getController();

        if (!class_exists($class)) {
            throw new \Exception("A classe '{$class}' não existe dentro do arquivo '{$file}'");
        }

        $controller = new $class();
        $controller->setRequest($this->getInput());
        $controller->setResponse($this->getResponse());
        $controller->setRenderer($this->getRenderer());

        if (!is_callable(array($controller, $this->getAction()))) {
            throw new \Exception("O método '{$this->getAction()}' não existe dentro da classe '{$class}' no arquivo '{$file}'");
        }
        
        call_user_func_array(array($controller, $this->getAction()), $this->getParams());
    }

    /**
     *   Retorna o caminho completo para o arquivo Controller
     *   @return string
     */
    public function getFilePath()
    {
        return $this->_directory . ucfirst($this->_controller).'.php';
    }

    public function setDirectory($directory)
    {
        $this->_directory = $directory;
        return $this;
    }

    /**
     *    Uma vez chamado esse metodo executa um controlador
     *    responsavel por apresentar um erro
     *    @return void
     */
    protected function _showErrorFileNotReadable()
    {
        $errorController = $this->_controllerFolder . 'Error404.php';

        if (!is_readable($errorController)) {
            throw new \Exception("Default error file not found '$errorController'");
        }

        require_once $errorController;
        $class = 'Error404';
        $method = 'index';
        $classInstance = new $class();
        $classInstance->{$method}($this->getParams());
    }
}
