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

use \Vita\Core\Session\SessionInterface;

class Vita extends VitaService
{
    const VERSION = '20201105-010203';

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
     * No Wakeup allowed
     */
    private function __wakeup()
    {
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
     * Returns Global Array Variables for de View
     *
     * @return array
     */
    public function getGlobalVars()
    {   
        return array(
            'base_url' => $this->getConfig()->get('url')
        );
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
}
