<?php

namespace System\Core\Router;

use Exception;
use System\Core\Renderer;
use System\Core\Request;
use System\Core\Response;

class RouterClassException extends Exception {}
class RouterClassMethodDoesNotExistsException extends Exception {}
class FileNotFoundException extends Exception {}

class Router
{
    /**
     * Nome do Arquivo Controller encontrado pelo Objeto Router
     * @var String
     */
    protected $_controller = null;

    /**
     * Método que sera chamado pelo Objeto Router
     * @var String
     */
    protected $_action = null;
    protected $_controllerFolder = null;
    protected $_params = array();
    protected $_baseUrl = null;

    /**
     * Diretórios Identificado como contendo o Controller
     * requisitado.
     * @var String
     */
    protected $_directory = null;

    /**
     * Guarda o status da requisição
     * @var integer - RouterStatus
     */
    protected $_dispatcher = 0;

    /**
     * @var Request
     */
    private $_input;

    /**
     * @var Response
     */
    private $_response;

    /**
     * @var Renderer
     */
    private $_renderer;

    /**
     * @var String
     */
    private $_request;

    /**
     * Construct
     *
     * @param String $controllerFolder - Pasta onde se encontram os Controllers
     * @param String $request - Algo como $_GET['request']
     */
    public function __construct($controllerFolder, $request)
    {
        $this->setBaseUrl();
        $this->setRequest($request);
        $this->setControllerFolder($controllerFolder);
        $this->_parseRequest();
    }

    /**
     * Seta qual o caminho para a pasta com os Controllers
     * @param string $folder
     * @return Router
     */
    public function setControllerFolder($folder)
    {
        $this->_controllerFolder = $folder;
        return $this;
    }

    /**
     * Retorna Pasta indicada pelo usuário para guardar os
     * Controllers.
     * @return String
     */
    public function getControllerFolder()
    {
        return $this->_controllerFolder;
    }

    /**
     * @param Request $input
     * @return $this
     */
    public function setInput(Request $input)
    {
        $this->_input = $input;
        return $this;
    }

    public function getInput()
    {
        return $this->_input;
    }

    public function setResponse(Response $response)
    {
        $this->_response = $response;
        return $this;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function setRenderer(Renderer $renderer)
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
     * Retorna a URL Base em que o sistema foi chamado
     * @return String
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
     * Método responsável por fazer o Parse da URL
     * recebida por este objeto.
     * @return $this
     */
    protected function _parseRequest()
    {
        $requestUri = $this->getRequest();
        if (null == $requestUri) {
            // set Defaults values
            $requestUri = 'index/index';
        }

        $arrRequest = explode('/', trim($requestUri, '/'));
        $this->setDirectory($this->getDirectoryFromRequestUrl($arrRequest));
        $request = $this->_parseRequestToArray($arrRequest);

        $this->setController($request['controller']);
        $this->setAction($request['action']);
        $this->setParams($request['params']);

        $this->_setDispatcher();
        return $this;
    }

    /**
     * @param array $request
     * @return array
     */
    protected function _parseRequestToArray($request)
    {
        $retorno = array();

        $retorno['controller'] = $request[0];
        array_shift($request);
        $retorno['action'] = isset($request[0]) ? $request[0] : 'index';
        array_shift($request);
        $retorno['params'] = empty($request) ? null : $request;

        return $retorno;
    }

    /**
     * Detecta o Diretório em que o Controller solicitado via URL
     * se encontra. Retorna caminho completo do Diretório.
     * @param $arrRequest
     * @return String
     */
    public function getDirectoryFromRequestUrl(&$arrRequest)
    {
        $directory = $this->getControllerFolder();

        $key = null;
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
    protected function _setDispatcher()
    {
        $filename = $this->getFilePath();
        
        if (!file_exists($filename)) {
            $this->_dispatcher = RouterStatus::CONTROLLER_NOT_FOUND;
            return;
        }

        require_once $filename;
        if (!class_exists($this->getController())) {
            $this->_dispatcher = RouterStatus::CLASS_NOT_FOUNT;
            return;
        }

        if (!is_callable(array($this->getController(), $this->getAction()))) {
            $this->_dispatcher = RouterStatus::METHOD_NOT_FOUND;
            return;
        }

        $this->_dispatcher = RouterStatus::FOUND;
    }

    /**
     * Uma vez chamado, esse método realiza o roteamento
     * e chama o Controller e o action requerido.
     * @return void
     * @throws RouterClassException
     * @throws Exception
     */
    public function init()
    {
        $file = $this->getFilePath();

        if (!is_readable($file)) {
            throw new Exception("File '{$file}' not Readable");
            return;
        }

        require_once $file;
        $class = $this->getController();

        if (!class_exists($class)) {
            throw new RouterClassException(
                "A classe '{$class}' não existe dentro do arquivo '{$file}'");
        }

        $controller = new $class();
        $controller->setRequest($this->getInput());
        $controller->setResponse($this->getResponse());
        $controller->setRenderer($this->getRenderer());

        if (!is_callable(array($controller, $this->getAction()))) {
            throw new RouterClassMethodDoesNotExistsException(
                "O método '{$this->getAction()}' não existe dentro da classe '{$class}' no arquivo '{$file}'");
        }
        
        call_user_func_array(array($controller, $this->getAction()), $this->getParams());
    }

    /**
     * Retorna o caminho completo para o arquivo Controller
     * @return string
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
     * Uma vez chamado esse método executa um controlador
     * responsável por apresentar um erro
     * @return void
     * @throws FileNotFoundException
     */
    protected function _showErrorFileNotReadable()
    {
        $errorController = $this->_controllerFolder . 'ErrorHandler.php';

        if (!is_readable($errorController)) {
            throw new FileNotFoundException("Router Error: file not found '$errorController'");
        }

        require_once $errorController;

        $class = 'ErrorHandler';
        $method = 'index';
        $classInstance = new $class();
        $classInstance->{$method}($this->getParams());
    }
}
