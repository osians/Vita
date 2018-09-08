<?php

namespace Framework\Vita\Core;

if (!defined('ALLOWED')) {
    exit('Acesso direto ao arquivo nao permitido.');
}

class FileException extends SysException {}

/**
 * Classe responsavel por criar logs no formato texto para o sistema.
 * por padrao a pasta onde os logs sao gravados se localiza em: sys/log/
 *
 * @todo  necessita implementar sistema de Tratamento de Exception
 */
class SYS_Log
{
    // @param String - /path/to/logs/folder/
    private $path = null;

    // @param String - Filename
    private $name = 'logfile.log';

    // @param Resource - File Pointer
    private $fp = null;

    // @param Object - This instance
    private static $instance;

    // @param String - Default Time Stamp
    private $default_time_zone = 'Brazil/East';
    
    // @param String - Formato de data hora
    private $date_format = 'Y-m-d H:i:s' ;

    /**
     * @param string $path - caminho para pasta de logs
     */
    public function __construct( $path = null ){
        self::$instance = $this;
        $this->setPath( $path );
    }

    public function setPath($path = null )
    {
        if(!is_null($path) && strlen($path) > 0):
            $this->path = $path ;
        else:
            $this->path = getcwd().DIRECTORY_SEPARATOR ;
        endif;
    }

    public function setFileName($__value__ = null ){
        if($__value__!= null) $this->name = $__value__ ;
    }

    public function settimezone($__value__ = null ){
        if($__value__!= null) $this->default_time_zone = $__value__ ;
    }

    public function __destruct(){
        $this->lclose();
    }

    public static function getInstance()
    {
        if(!isset(self::$instance))
            self::$instance = new self();
        return self::$instance;
    }

    private function lopen()
    {
        date_default_timezone_set( $this->default_time_zone );
        $arquivo = $this->path.date('Ymd').'_'.$this->name;
        try
        {
            // tentando abrir arquivo, ou criar caso nao exista
            $this->fp = fopen( $arquivo, 'a' );
            if(!$this->fp)
                throw new FileException( 'Erro ao abrir o arquivo de log : ' . $arquivo );
        }catch(FileException $e){
            echo $e->getMessage();
        }
        return $this->fp ;
    }

    private function lclose($fp = null){
        return ($fp!=null && is_resource($fp)) ? fclose($fp) :
        (($this->fp!=null && is_resource($this->fp)) ? fclose($this->fp) : true);
    }

    /**
     * Seta o caminhoe nome do arquivo de log
     * @param string $path - /caminho/para/o/arquivo/e/log
     */
    public function lfile( $path )
    {
        $this->path = $path;
    }

    /**
     * escreve uma mensagem no arquivo de log do sistema
     * @param string $message
     * @param string $file - (optional) nome do arquivo de onde vem a solicitacao
     * @return boolean
     */
    public function write( $message = null, $file = null )
    {
        if($message == null ) return false;

        if(is_null($this->path))
            $this->setPath(null);

        $fp = $this->lopen();

        if(!is_null($file)){
            $script_name = trim($file);
        }else{
            // verificando nome do arquivo que esta solicitando gravacao
            $script_name = pathinfo( $_SERVER['PHP_SELF'], PATHINFO_FILENAME );
        }

        date_default_timezone_set( $this->default_time_zone );
        $time = date( $this->date_format );
        $message = str_replace(array("\r", "\n","\t"), " ", $message);
        // retirando mais de um espaco
        $message = preg_replace('!\s+!', ' ', $message );

        // retorna o numero de bytes escritos ou false
        $result = fwrite($fp, "$time ($script_name) $message \n" );

        $this->lclose( $fp );
        return $result;
    }

    // evitando que a classe seja clonada
    private function __clone(){}
}