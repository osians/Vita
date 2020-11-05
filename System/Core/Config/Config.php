<?php

namespace Vita\Core\Config;

class Config
{
    /**
     * Guarda todas as configurações da Aplicação
     * @var array
     */
    protected $_configs = array();

    /**
     * Repositório com as configurações do sistema
     * @var ConfigRepositoryInterface
     */
    protected $_configRepository;

    /**
     *  Constructor
     *  @param ConfigRepositoryInterface $repository
     */
    public function __construct(ConfigRepositoryInterface $repository)
    {
        $this->_configRepository = $repository;
        $this->init();
    }

    /**
     * Converte os dados de um array contido no repositório,
     * para configurações acessíveis dentro dessa classe.
     * @return void
     */
    public function init()
    {
        foreach ($this->_configRepository->getAll() as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Seta uma Configuração na classe
     * @param string $name
     * @param mixed $value
     * @return Config
     */
    public function set($name, $value)
    {
        $name = $this->_stringToKey($name);
        $this->_configs[$name] = $value;
        return $this;
    }

    /**
     * Retorna o valor de uma Configuração dado seu nome/key
     *
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $name = $this->_stringToKey($name);

        if (!isset($this->_configs[$name])) {
            return $default;
        }

        return $this->_configs[$name];
    }

    /**
     * Recebe uma String e cria uma chave para Arrays
     * a partir dela mantendo apenas letras, hífen e underline
     *
     * @param  string $string
     * @return string
     */
    private function _stringToKey($string)
    {
        return str_replace(' ','',
            strtolower(preg_replace("/[^a-zA-Z_-]+/", "", $string))
        );
    }

    /**
     * Método Magico get. Ira direcionar para o método
     * get existente na classe.
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Método Magico set. Ira direcionar para o método Set
     * existente na classe.
     * @param string $name
     * @param mixed $value
     * @return Config
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
        return $this;
    }
    
    /**
     * Persist Instance Config Data
     * 
     * @return Config
     */
    public function save()
    {
        $this->_configRepository->save($this->_configs);
        return $this;
    }
}
