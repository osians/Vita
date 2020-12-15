<?php 

class Autoloader
{
    /**
     * Directory to look for php files
     *
     * @var array
     */
    protected $_folders = array();

    /**
     * Retorna uma instância única de uma classe.
     *
     * @static Singleton $instance - instância única dessa classe.
     *
     * @return Autoloader - Instância única.
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Construtor do tipo protegido previne que uma nova instância da
     * Classe seja criada através do operador `new` de fora dessa classe.
     */
    protected function __construct()
    {
        spl_autoload_register(array($this, 'loader'));
    }

    /**
     * Método clone do tipo privado previne a clonagem dessa instância
     * da classe
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Método unserialize do tipo privado para prevenir a desserialização
     * da instância dessa classe.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

    /**
     * Método que carrega os arquivos php
     *
     * @param $classname
     * @return void
     */
    public function loader($classname)
    {
        if (strpos($classname, 'System') === 0) {
            $classname = str_replace('System', '', $classname);
        }

        foreach ($this->getFolders() as $dir) {
            $filename = $dir[0] . str_replace('\\', '/', $classname) .'.php';

            if (!empty($dir[1])) {
                $filename = str_replace($dir[1], null, $filename);
            }

            if (file_exists($filename)) {
                require_once $filename;
                break;
            }
        }
    }

    /**
     * Add Autoloader Folder
     *
     * @param string $folder - path
     * @param string $excludeFromPath - remove from Path on autoload
     * @return Autoloader
     */
    public function addFolder($folder, $excludeFromPath = '')
    {
        $this->_folders[] = [$folder, $excludeFromPath];
        return $this;
    }

    /**
     * Get Folders
     *
     * @return array
     */
    public function getFolders()
    {
        return $this->_folders;
    }
}

/** Adicionando as pastas iniciais  */
Autoloader::getInstance()->addFolder(__DIR__ . '/Library/whoops/src/');
Autoloader::getInstance()->addFolder(__DIR__ . '/Library/veManager/src/', 'Osians/VeManager/');
Autoloader::getInstance()->addFolder(__DIR__ . '/');
