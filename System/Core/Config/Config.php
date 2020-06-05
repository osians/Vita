<?php

namespace Vita\Core\Config;

class Config
{
    /**
     *    Guarda todas as Configuracoes da Aplicacao
     *    @var array
     */
    protected $_configs = array();

    /**
     *    Repositorio com as configuracoes do sistema
     *    @var ConfigRepositoryInterface
     */
    protected $_configRepository;

    /**
     *    Constructor
     *    @param ConfigRepositoryInterface $repository
     */
    public function __construct(ConfigRepositoryInterface $repository)
    {
        $this->_configRepository = $repository;
        $this->init();
    }

    /**
     *    Converte os dados de um array contido no repositorio,
     *    para configuracoes acessiveis dentro dessa classe.
     *    @return void
     */
    public function init()
    {
        foreach ($this->_configRepository->getAll() as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     *    Seta uma Configuracao na classe
     *    @param string $name
     *    @param mixed $value
     */
    public function set($name, $value)
    {
        $name = $this->_stringToKey($name);
        $this->_configs[$name] = $value;
        return $this;
    }

    /**
     *    Retorna o valor de uma Configuração dado seu nome/key
     *    @param  string $name
     *    @return mixed
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
     *    Recebe uma String e cria uma chave para Arrays
     *    a partir dela mantendo apenas letras, hifen e underline
     *    @param  string $string
     *    @return string
     */
    private function _stringToKey($string)
    {
        return str_replace(' ','',
            strtolower(preg_replace("/[^a-zA-Z_-]+/", "", $string))
        );
    }

    /**
     *    Metodo Magico get. Ira direcionar para o metodo
     *    get existente na classe.
     *    @param  string $name
     *    @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     *    Metodo Magico set. Ira direcionar oara o metodo Set
     *    existente na classe.
     *    @param string $name
     *    @param mixed $value
     *    @return Config
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
