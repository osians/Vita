<?php

namespace System\Core;

class Controller
{
    protected $_request;
    protected $_response;

    /**
     * @var Renderer
     */
    protected $_renderer;

    public function __construct() {}

    public function setRequest(RequestInterface $request)
    {
        $this->_request = $request;
        return $this;
    }

    public function getRequest()
    {
        return $this->_request;
    }

    public function setResponse(ResponseInterface $response)
    {
        $this->_response = $response;
        return $this;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function setRenderer(RendererInterface $renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }

    /**
     * Returns Renderer Object
     *
     * @return Renderer
     */
    public function getRenderer()
    {
        return $this->_renderer;
    }

    public function getEntityManager()
    {
        return \Vita\Vita::getInstance()->getEntityManager();
    }

    public function loadModel($model)
    {
        $config = \Vita\Vita::getInstance()->getConfig();
        
        $modelFolder = $config->get('app_folder') . 
            $config->get('model_folder') . DIRECTORY_SEPARATOR;

        $filename = "{$modelFolder}{$model}.php";
        
        if (!file_exists($filename)) {
            throw new \Exception("Model {$model} not found at {$modelFolder}");
        }

        require_once $filename;
        return $this;
    }

    /**
     * Return a New Query Builder
     * 
     * @return \Wsantana\VeManager\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return \Vita\Vita::getQueryBuilder();
    }

    /**
     *    Loads a Library as property of this Class
     *
     *    @param  String $libraryName
     *    @param  String $alias
     *    @return \System\Core\Controller
     */
    public function loadLibrary($libraryName, $alias = null)
    {
        $config = \Vita\Vita::getInstance()->getConfig();
        $libFolder = $config->get('app_folder') . $config->get('library_folder') . DIRECTORY_SEPARATOR;
        $filename = "{$libFolder}{$libraryName}.php";

        if (!file_exists($filename)) {
            throw new \Exception("Library {$libraryName} not found at {$libFolder}");
        }

        require_once $filename;
        $property = null !== $alias ? strtolower($alias) : strtolower($libraryName);
        $this->$property = new $libraryName(); 

        return $this;
    }

    /**
     *    Loads a Helper File
     *
     *    @param  String $helper
     *
     *    @return Controller
     *
     *    @throws \Exception - On Helper not Found
     */
    public function loadHelper($helper)
    {
        $config = \Vita\Vita::getInstance()->getConfig();
        
        $folder = $config->get('app_folder') . 
            $config->get('helper_folder') . DIRECTORY_SEPARATOR;

        $filename = "{$folder}{$helper}.php";
        
        if (!file_exists($filename)) {
            throw new \Exception("Helper {$helper} not found at {$folder}");
        }

        require_once $filename;
        return $this;
    }
}
