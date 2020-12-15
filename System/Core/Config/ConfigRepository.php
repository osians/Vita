<?php

namespace System\Core\Config;

use System\Core\Config\ConfigRepositoryInterface;

class ConfigRepository implements ConfigRepositoryInterface
{
    /**
     * Nome do Arquivo de Configuração
     * @var String
     */
    protected $_file = 'vita-config.php';
    
    /**
     * Retorna caminho completo para o arquivo de Configuracoes
     *
     * @return String
     */
    public function getFile()
    {
        return __DIR__ . "/{$this->_file}";
    }
    
    /**
     * @see ConfigRepositoryInterface::getAll()
     * @return Array
     */
    public function getAll()
    {
        $configs = array();
        require_once $this->getFile();
        return $configs;
    }

    /**
     * Esse metodo le o arquivo de configuracoes PHP e percorre cada linha
     * identificando as configuracoes e verificando se foram alteradas,
     * no caso de serem alteradas o algoritmo gera uma nova linha que 
     * sera salva no arquivo config.php
     *
     * @see ConfigRepositoryInterface::save()
     * @return ConfigRepository
     */    
    public function save($config = array())
    {
        if (empty($config)) {
            return $this;
        }
        
        // carregando arquivo vita-config.php para um array
        $configFileContent = file($this->getFile(), FILE_IGNORE_NEW_LINES);
        $newContent = '';
        
        foreach ($configFileContent as $linha) {
            $line = trim($linha);
            
            // se nao encontrarmos a variavel $configs no arquivo ...
            if (strpos($line, '$configs') === false) {
                $newContent .= $line . PHP_EOL;
                continue;
            }
            
            // identificando key e value no arquivo config
            list($key, $value) = explode('=', $line);
            preg_match_all("/\[[^\]]*\]/", $key, $matches);
            $key = str_replace(["\"", "[", "]", "'"], '', trim($matches[0][0]));
            
            // procura por comentarios, em casos como $configs['a'] = 10; // comment
            $comentario = substr($value, strpos($value, ";") + 1);
            
            // criando nova linha a gravar no aruivo config
            $newContent .= '$configs[\'' . $key . '\'] = ' . $this->_parseValue($config[$key]) . ";{$comentario}" . PHP_EOL;
        }
        
        ConfigRepository::rewrite($this->getFile(), $newContent);
        
        return $this;
    }
    
    /**
     * Realiza parte de um valor para retornar uma string
     * que sera adicionada ao arquivo final
     *
     * @param mixed $value
     *
     * @return string
     */
    private function _parseValue($value)
    {
        if (is_bool($value)) {
            return ($value) ? 'true' : 'false';
        }
        
        if (is_string($value)) {
            return "'{$value}'";
        }

        if (is_numeric($value)) {
            return intval($value);
        }
        
        return $value;
    }
    
    /**
     * Rewrite model into file
     *
     * @param String $fileName
     * @param Stirng $content
     *
     * @throws Exception
     */
    public static function rewrite($fileName, $content)
    {
        $fp = fopen($fileName, 'w');
        
        if (!$fp) {
            throw new Exception('Cant open file for write');
        }

        $startTime = microtime(TRUE);

        do {
            $canWrite = flock($fp, LOCK_EX);
            if (!$canWrite) {
                usleep(round(rand(0, 100)*1000));
            }
        } while ((!$canWrite) && ((microtime(TRUE)-$startTime) < 5));

        if ($canWrite) {            
            fwrite($fp, $content);
            flock($fp, LOCK_UN);
        }

        fclose($fp);
    }
}
