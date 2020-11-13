<?php

namespace Vita;

/* ---------------------------
 -- Vita brevis,
 -- ars longa,
 -- occasio praeceps,
 -- experimentum periculosum,
 -- iudicium difficile.
                 (Hipócrates)
 --------------------------- */

use Vita\Core\Config\Config;
use Vita\Core\Router\Router;
use \Vita\Core\Session\SessionInterface;

class Vita extends VitaService
{
    const VERSION = '20201105-010203';

    /**
     * @var Router
     */
    private $_router = null;

    /**
     * Keeps Global vars
     * @var array
     */
    private $_globals = array();

    /**
     * Unique Instance
     *
     * @var Vita
     */
    private static $instance;

    /**
     * Returns Unique Vita Instance
     *
     * @return Vita
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * No Construct allowed
     */
    private function __construct()
    {
    }

    /**
     * No Clone allowed
     */    
    private function __clone()
    {
    }

    /**
     * @inheritDoc
     */
    public function init(Config $config)
    {
        parent::init($config);
        $this->_initSystemGlobalVars();
        return $this;
    }

    /**
     * @param Router $router
     * @return $this
     */
    public function setRouter(Core\Router\Router $router)
    {
        $this->_router = $router;
        return $this;
    }

    /**
     * @return Router|null
     */
    public function getRouter()
    {
        return $this->_router;
    }

    /**
     * Set Session Manager
     *
     * @param SessionInterface $session
     *
     * @return Vita
     */
    public function setSession(SessionInterface $session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     * @return $this
     */
    protected function _initSystemGlobalVars()
    {
        $this->set('base_url', $this->getConfig()->get('url'));
        return $this;
    }

    /**
     * Returns Global Array Variables for de View
     * @return array
     */
    public function getGlobalVars()
    {
        return $this->_globals;
    }

    /**
     * Keeps Global Vars
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->_globals[$key] = $value;
        return $this;
    }

    /**
     * Magic Method GET
     *
     * @param mixed $key
     *
     * @return mixed|null
     */
    public function __get($key)
    {
        $objectVars = (array_keys(get_object_vars($this)));
        $property = "_{$key}";
        if (in_array($property, $objectVars)) {
            return $this->$property;
        }
        return null;
    }

    /**
     * Get System current Version
     * @return string
     */
    public static function getVersion()
    {
        return Vita::VERSION;
    }
}
