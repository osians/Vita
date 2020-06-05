<?php

namespace Vita\Core\Log;

/**
 * Classe responsavel por criar logs no formato texto para o sistema.
 * por padrao a pasta onde os logs sao gravados se localiza em: sys/log/
 *
 * @todo  necessita implementar sistema de Tratamento de Exception
 */
class Log
{
    /**
     *    Caminho/Pasta em que serao gravados os Logs
     *    @var string - /path/to/logs/folder/
     */
    private $_logFolder = null;

    /**
     *    Nome do arquivo a guardar os Logs
     *    @var string
     */
    private $_filename = 'logfile.log';

    /**
     *    Ponteiro para o Arquivo
     *    @var Resource - File Pointer
     */
    private $_fp = null;

    /**
     *    Timestamp default a ser usado para Data
     *    @var string
     */
    private $_defaultTimezone = 'Brazil/East';
    
    /**
     *    Formato de Data usado para registrar o Log dentro do arquivo
     *    @var string
     */
    private $_dateFormat = 'Y-m-d H:i:s';

    /**
     *    Guarda o resultado da ultima escrita,
     *    numero de bytes escritos ou false em caso de erro
     *    @var mixed - int|false
     */
    private $_lastResult = null;

    /**
     *    Constructor
     *    @param string $logFolder - caminho para pasta de logs
     */
    public function __construct($logFolder = null)
    {
        $this->setFolder($logFolder);
    }

    /**
     *    Seta a Pasta que ira guardar os arquivos de log
     *    @param string $logFolder
     *    @return Log
     */
    public function setFolder($logFolder = null)
    {
        if (!$this->_isValidLogFolder($logFolder)) {
            // @todo - informar em log que esta mudando a pasta automaticamente
            // para um caminho diferente uma vez que o valor informado nao e' valido.
            $ds = DIRECTORY_SEPARATOR;
            $logFolder = __DIR__ . "{$ds}Logs{$ds}";
        }

        $this->_logFolder = $logFolder;
        return $this;
    }

    /**
     *    Verifica se a Pasta informada para guardar os Logs é valida
     *    @param string $logFolder
     *    @return boolean
     */
    private function _isValidLogFolder($logFolder)
    {
        // @todo - verificar se a pasta existe!
        return (!is_null($logFolder) && strlen($logFolder) > 0);
    }

    /**
     *    Seta o nome de arquivo em que serão gravados os Logs
     *    @param string $filename
     *    @return Log
     */
    public function setFilename($filename = null)
    {
        if ($filename != null) {
            $this->_filename = $filename;
        }
        return $this;
    }

    /**
     *    Seta o Timestamp
     *    @param String $timezone - Ex. Brazil/East
     */
    public function setTimezone($timezone = null)
    {
        if ($timezone != null) {
             $this->_defaultTimezone = $timezone;
        }
        return $this;
    }

    /**
     *    Abre o arquivo para escrita
     *    @return Resource - FilePointer
     */
    private function _open()
    {
        try {
            
            $arquivo = $this->_getFullFilepath();
            /**
             * lembrando que a pasta onde sera gravado o log
             * precisa de acesso ao user www-data
             * chown -R www-data:www-data folder
             * chmod -R g+w folder
             **/
            $this->_fp = fopen($arquivo, 'a');
            if (!$this->_fp) {
                throw new FileException('Erro ao abrir o arquivo de log : ' . $arquivo);
            }

        } catch (FileException $e) {
            echo $e->getMessage();
        }
        return $this->_fp;
    }

    /**
     *    Retorna o caminho completo ate o arquivo de log
     *    @return String
     */
    private function _getFullFilepath()
    {
        date_default_timezone_set($this->_defaultTimezone);
        return $this->_logFolder.date('Ymd').'_'.$this->_filename;
    }

    /**
     *    Fecha o arquivo e Destroi o ponteiro para ele
     *    @param  resource $fp
     *    @return bool
     */
    private function _close($fp = null)
    {
        if ($fp != null && is_resource($fp)) {
            return fclose($fp);
        }

        if ($this->_fp != null && is_resource($this->_fp)) {
            fclose($this->_fp);
        }

        return false;
    }

    /**
     *    Escreve uma mensagem no arquivo de log do sistema
     *    @param string $message
     *    @param string $file - (optional) nome do arquivo de onde vem a solicitacao
     *    @return boolean
     */
    public function write($message, $file = null)
    {
        if (is_null($this->_logFolder)) {
            $this->setFolder(null);
        }

        $fp = $this->_open();

        if (!is_null($file)) {
            $script_name = trim($file);
        } else {
            // verificando nome do arquivo que esta solicitando gravacao
            $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        }

        date_default_timezone_set($this->_defaultTimezone);
        $time = date($this->_dateFormat);
        $message = str_replace(array("\r", "\n","\t"), " ", $message);
        // retirando mais de um espaco
        $message = preg_replace('!\s+!', ' ', $message);

        $this->_lastResult = fwrite($fp, "$time ($script_name) $message \n");

        $this->_close($fp);
        return $this;
    }

    /**
     *    Evitando que a classe seja clonada
     *    @return void
     */
    private function __clone()
    {
    }

    /**
     *   Garante que o Resource sera fechado
     */
    public function __destruct()
    {
        $this->_close();
    }
}
