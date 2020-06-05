<?php 

class Autoloader
{
    /**
     *    Directoy to look for php files
     *
     *    @var array
     */
    protected $_folders = array();

    /**
     *    Retorna uma instância única de uma classe.
     *
     *    @static Singleton $instance - instância única dessa classe.
     *
     *    @return Singleton - Instância única.
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
     *    Construtor do tipo protegido previne que uma nova instância da
     *    Classe seja criada através do operador `new` de fora dessa classe.
     */
    protected function __construct()
    {
        spl_autoload_register(array($this, 'loader'));
    }

    /**
     *    Método clone do tipo privado previne a clonagem dessa instância
     *    da classe
     *
     *    @return void
     */
    private function __clone()
    {
    }

    /**
     *    Método unserialize do tipo privado para prevenir a desserialização
     *    da instância dessa classe.
     *
     *    @return void
     */
    private function __wakeup()
    {
    }

    /**
     *    Metodo que carrega os arquivos php
     *
     *    @return void
     **/
    public function loader($classname)
    {
        if (strpos($classname, 'Vita') === 0) {
            $classname = str_replace('Vita', '', $classname);
        }

        foreach ($this->getFolders() as $dir) {
            $filename = $dir . str_replace('\\', '/', $classname) .'.php';
            if (file_exists($filename)) {
                require_once $filename;
                break;
            }
        }
    }

    /**
     * Add Autoloader Folder
     * 
     * @param String $folder
     *
     * @return Autoloader
     */
    public function addFolder($folder)
    {
        $this->_folders[] = $folder;
        return $this;
    }

    /**
     * Get Folders
     *
     * @return Array
     */
    public function getFolders()
    {
        return $this->_folders;
    }
}

/** Adicionando as pastas iniciais  */
Autoloader::getInstance()->addFolder(__DIR__ . '/Library/whoops/src/');
Autoloader::getInstance()->addFolder(__DIR__ . '/Library/veManager/src/');
Autoloader::getInstance()->addFolder(__DIR__);
