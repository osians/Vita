<?php

namespace Vita\Core\Session;

/**
 * PHP Session Manager
 *
 * @package Vita
 * @subpackage Core
 * @category Sessions
 * @author Wanderlei Santana <sans.pds@gmail.com>
 * @link https://github.com/osians
 * @since 202006040130
 **/
class Session implements \Vita\Core\Session\SessionInterface
{
    /**
     *    Unique Instance
     *
     *    @var Session
     */
    private static $_instance;
    
    /**
     *    Expires session after 10 minutes(in Seconds)
     *
     *   @var Integer
     **/
    private $_sessionExpireTime = 600;

    /**
     *    Session Hijacke Data
     *
     *    @var Array
     */
    protected $_data;

    /**
     *    If true pull all cables from the 
     *    server and unplug the power
     *
     *    @var boolean
     */
    protected $_hijacked = false;


    /**
     *    Construct
     *
     *    @param Integer $expireTime
     *
     *    @return void
     **/
    public function __construct($expireTime = null, $key = 'fingerprint')
    {
        self::$_instance =& $this;

        $this->setSessionExpireTime($expireTime);
        $this->_makeSessionSafe();
        
        if (!isset($_SESSION)) {
            session_start();
        }
        session_regenerate_id();

        $this->_checkFingerprint($key);
        $this->_checkSessionExpireTime();
    }

    /**
     *    Disable some configs to make session more safe
     *
     *    @return Session
     */
    protected function _makeSessionSafe()
    {
        // Security is king
        ini_set('session.cookie_httponly',  1);
        ini_set('session.cookie_lifetime',  0);
        ini_set('session.use_trans_sid',    0);
        ini_set('session.use_strict_mode',  1);
        ini_set('session.use_cookies',      1);
        ini_set('session.use_only_cookies', 1);

        if ($this->_isHttps()) {
            ini_set('session.cookie_secure', 1);
        }

        if (in_array('sha512', hash_algos())) {
            ini_set('session.hash_function', 'sha512');
        }

        header("Cache-control: private"); 

        return $this;
    }

    /**
     *    Check if Connection is runnig over https protocol
     *
     *    @return boolean
     */
    private function _isHttps()
    {
        return ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443)
        ? true
        : false;
    }

    /**
     *    Check Fingerprint to avoid Hijacking
     *
     *    @param String $key
     *
     *    @return Session
     */
    protected function _checkFingerprint($key)
    {
        $this->_data = &$_SESSION[$key];

        if (null === $this->_data) {
            $this->_data = new \StdClass;
        }

        if (empty($this->_data->fingerprint)) {
            return $this->_generateFingerprint();
        }

        return $this->_verifyFingerprint();
    }

    /**
     *    Generate a fingerprint based on timestamp and user agent.
     *
     *    @source https://github.com/CVM/Session-Security/blob/master/session_security.php
     * 
     *    @param int $time
     */
    protected function _generateFingerprint($time = 0)
    {
        // If no timestamp is provided, assume current time.
        if ($time === 0) {
            $time = time();
        }

        // Create data arrays to pick values from to form the basis for the fingerprint.
        $fingers = explode(' ', $_SERVER['HTTP_USER_AGENT']);
        $fingers2 = explode(',', $_SERVER['HTTP_USER_AGENT']);

        // Based on the time, select the array indexes to use.      
        $i = $time % count($fingers);
        $j = $time % count($fingers2);

        // Generate the fingerprint and save the timestamp (so the process is repeatable).
        $this->_data->fingerprint = sha1($fingers[$i] . $fingers2[$j] . $time);
        $this->_data->time = $time;

        return $this;
    }

    /**
     *    Verify the current fingerprint to ensure that we 
     *    have the same user agent by regenerating the previous
     *    fingerprint using the stored timestamp.
     */
    protected function _verifyFingerprint()
    {
        // Save the last fingerprint for comparison and 
        // regenerate the fingerprint based on the stored timestamp.
        $lastFingerprint = $this->_data->fingerprint;

        $this->_generateFingerprint($this->_data->time);

        // If the request originated from the same user, they should match.
        if ($lastFingerprint === $this->_data->fingerprint) {
            // Generate a new fingerprint for next load (to tighten the window of opportunity).
            return $this->_generateFingerprint();
        } 

        // Fingerprint mismatch. Move to a new session ID and clear the data.
        $this->destroy();
        session_start();
        
        // Set object status to denote session hijack was attempted.
        $this->_hijacked = true;

        return $this;
    }

    /**
     *    Was a session hijack attempt detected?
     * 
     *    @return bool
     */
    public function isHijacked()
    {
        return $this->_hijacked;
    }

    /**
     *    Check if Session has expired and destroy it
     *
     *    @return void
     */
    protected function _checkSessionExpireTime()
    {
        $expired = isset($_SESSION['SS_LAST_ACTIVITY']) &&
        (time() - $_SESSION['SS_LAST_ACTIVITY'] > $this->getExpiretime());

        if ($expired) {
            $this->destroy();
        }

        $_SESSION['SS_LAST_ACTIVITY'] = time();
    }

    /**
     *   Set session Expire time in Seconds
     *
     *   @param Integer $value
     *
     *   @return Session
     **/
    public function setSessionExpireTime($value = null)
    {
        if ($value != null) {
            $this->_sessionExpireTime = $value;
        }
        return $this;
    }

    /**
     *    Get Session Expire Time
     *
     *    @return Integer
     **/
    public function getExpiretime()
    {
        return $this->_sessionExpireTime;
    }

    /**
     *    Singleton
     *
     *    @return Session
     **/
    public static function getInstance($expireTime = null)
    {
        if(!isset(self::$_instance)) {
            self::$_instance = new self($expireTime);
        }
        return self::$_instance;
    }

    /**
     *    Set a value in the session
     *
     *    @param String $name
     *    @param Mixed $value
     *
     *    @return Session
     **/
    public function set($name, $value)
    {
        $_SESSION[trim($name)] = $value;    
        return $this;
    }

    /**
     *   Get value From Session by name
     *
     *   @param String $name
     *
     *   @return Mixed | null
     */
    public function get($name = null)
    {
        if($name == null) {
            return null;
        }
        
        if(isset($_SESSION[trim($name)])) {
            return $_SESSION[trim($name)];
        }

        return null;
    }

    /**
     *   Removes value from Session Array
     *
     *   @param String $name
     *
     *   @return Session
     **/
    public function del($name)
    {
        unset($_SESSION[trim($name)]);
        return $this;
    }
    
    /**
     *   magic method
     *
     *   @return Session
     **/
    public function __set($name, $value)
    {
        $this->set($name, $value);
        return $this;
    }

    /**
     *   Magic method
     *
     *   @return Mixed
     **/
    public function __get($name)
    {
        return $this->get($name);
    }
    
    /**
     *   Destroy Session
     *
     *   @see SessionInterface::destroy()
     *
     *   @return void
     **/
    function destroy()
    {
        $_SESSION = array();
        session_regenerate_id();
        session_destroy();

        return $this;
    }
}