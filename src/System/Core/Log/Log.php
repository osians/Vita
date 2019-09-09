<?php

namespace Vita\System\Core\Log;

/**
 * Classe responsavel por criar logs no formato texto para o sistema.
 * por padrao a pasta onde os logs sao gravados se localiza em: sys/log/
 *
 * @todo  necessita implementar sistema de Tratamento de Exception
 */
class Log
{
    // @param String - /path/to/logs/folder/
    private $logFolder = null;

    // @param String - Filename
    private $filename = 'logfile.log';

    // @param Resource - File Pointer
    private $fp = null;

    // @param Object - This instance
    private static $instance;

    // @param String - Default Time Stamp
    private $defaultTimezone = 'Brazil/East';
    
    // @param String - Formato de data hora
    private $date_format = 'Y-m-d H:i:s' ;

    /**
     *    @param string $logFolder - caminho para pasta de logs
     */
    public function __construct($logFolder = null)
    {
        self::$instance = $this;
        $this->setLogFolder($logFolder);
    }

    public function setLogFolder($logFolder = null)
    {
        if (!is_null($logFolder) && strlen($logFolder) > 0) {
            $this->logFolder = $logFolder ;
        } else {
            $this->logFolder = __DIR__ . DIRECTORY_SEPARATOR . 'Logs' . DIRECTORY_SEPARATOR ;
        }
    }

    public function setFilename($filename = null)
    {
        if ($filename != null) {
            $this->filename = $filename;
        }
    }

    public function setTimezone($timezone = null)
    {
        if ($timezone != null) {
             $this->defaultTimezone = $timezone ;
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function lopen()
    {
        date_default_timezone_set($this->defaultTimezone);
        $arquivo = $this->logFolder.date('Ymd').'_'.$this->filename;

        try {
            $this->fp = fopen($arquivo, 'a');
            if (!$this->fp) {
                throw new FileException('Erro ao abrir o arquivo de log : ' . $arquivo);
            }
        } catch (FileException $e) {
            echo $e->getMessage();
        }
        return $this->fp;
    }

    private function lclose($fp = null)
    {
        return ($fp!=null && is_resource($fp))
            ? fclose($fp)
            : (($this->fp!=null && is_resource($this->fp)) ? fclose($this->fp) : true);
    }

    /**
     * escreve uma mensagem no arquivo de log do sistema
     * @param string $message
     * @param string $file - (optional) nome do arquivo de onde vem a solicitacao
     * @return boolean
     */
    public function write($message = null, $file = null)
    {
        if ($message == null) {
            return false;
        }

        if (is_null($this->logFolder)) {
            $this->setLogFolder(null);
        }

        $fp = $this->lopen();

        if (!is_null($file)) {
            $script_name = trim($file);
        } else {
            // verificando nome do arquivo que esta solicitando gravacao
            $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        }

        date_default_timezone_set($this->defaultTimezone);
        $time = date($this->date_format);
        $message = str_replace(array("\r", "\n","\t"), " ", $message);
        // retirando mais de um espaco
        $message = preg_replace('!\s+!', ' ', $message);

        // retorna o numero de bytes escritos ou false
        $result = fwrite($fp, "$time ($script_name) $message \n");

        $this->lclose($fp);
        return $result;
    }

    // evitando que a classe seja clonada
    private function __clone()
    {
    }

    public function __destruct()
    {
        $this->lclose();
    }
}
