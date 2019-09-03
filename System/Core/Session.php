<?php

namespace Vita\System\Core;

use \Vita\System\Core\Config\SysVitaConfigVisibilidadeEnum;

class Session
{
    private static $instance;
    private $session_expire_time = null ;
    private $mode = SysVitaConfigVisibilidadeEnum::PRIVADA;

    public function __construct($expireTime = null)
    {
        // evita ser instanciado duas vezes
        self::$instance =& $this;

        $this->setExpiretime($expireTime);

        session_start();
        header("Cache-control: private");
        // troca o ID da sessao a cada refresh
        // quando fecha browser destroi sessao
        // impede roubo de sessao
        session_regenerate_id();
        // setando arquivo ini( evita JS acessar sessao)
        ini_set('session.cookie_httponly', true);
        ini_set('session.use_only_cookies', true);
        // verificando se sessao esta configurada para expirar apos inatividade
        if(defined('SESSION_EXPIRE_TIME') && SESSION_EXPIRE_TIME > 0):
            // verificando se sessao nao expirou por tempo
            if( isset($_SESSION['SS_LAST_ACTIVITY']) &&
                (time() - $_SESSION['SS_LAST_ACTIVITY'] >
                SESSION_EXPIRE_TIME)):
                // destroy sessao
                $this->destroy();
            endif;
        endif;
        // setando ultima atividade no sistema
        $_SESSION['SS_LAST_ACTIVITY'] = time();
    }

    public function setExpiretime($__value__ = null){
        if($__value__ != null) $this->session_expire_time = $__value__;
    }

    public function getExpiretime(){
        return $this->session_expire_time ;
    }

    public static function getInstance($expireTime = null)
    {
        if(!isset(self::$instance))
            self::$instance = new self($expireTime);
        return self::$instance;
    }

    public function __set($name, $value){
        $this->set($name,$value);
    }

    public function __get($name){
        return $this->get($name);
    }

    public function set($__name, $__value){
        if($this->mode == SysVitaConfigVisibilidadeEnum::PRIVADA)
            $_SESSION[trim($__name)] = $__value;
        else
            $this->$__name = $__value;
    }

    public function get($__name = null){
        if($__name == null) return null;
        if(isset($_SESSION[trim($__name)]))
            return $_SESSION[trim($__name)];
        else
            return null;
    }

    public function publicar(){
        $this->mode = SysVitaConfigVisibilidadeEnum::PUBLICA;
        foreach ($_SESSION as $key => $value)
            $this->$key = $value;
    }

    public function del($name){
        unset($_SESSION[trim($name)]);
    }

    function destroy()
    {
        $_SESSION = array();
        session_destroy();
        //session_regenerate_id();
    }
}