<?php

namespace Vita;

/* ---------------------------
 -- Vita brevis,
 -- ars longa,
 -- occasio praeceps,
 -- experimentum periculosum,
 -- iudicium difficile.
                 (Hipócrates)
 --------------------------- */

// require_once 'Bootstrap.php';

use \Vita\Core\Config\Config;
//use \Vita\Core\SysException;
//use \Vita\Core\SYS_Table;
//use \Exception;
use \Vita\Core\SessionInterface;
use \Vita\Core\Session;

class Vita
{
    const VERSION = '20200605-010203';

    /**
     * Log info data into Files
     *
     * @var object Vita\Core\Log
     */
    protected $_log = null;

    /**
     * Config System Manager
     *
     * @var object Config
     */
    protected $_config = null;

    /**
     * PHP Session Manager
     * 
     * @var Session
     */
    protected $_session = null;

    /**
     * Data Validate
     *
     * @var object Validate
     */
    protected $_validate = null;

    /**
     * Data Sanitize
     *
     * @var object Validate
     */
    protected $_sanitize = null;

    /**
     * File Upload Object
     *
     * @var Vita\Core\Upload
     */
    protected $_upload = null;

    /**
     * Gerencia acesso a database
     *
     * @var object \EntityManager
     */
    protected $_entityManager = null;

    /**
     * Utils
     *
     * @var Vita\Core\Utils
     */
    protected $_utils = null;

    /**
     * Quando um Formulário emite um POST, este
     * Objeto trata o Post, limpa os dados de
     * caracteres indesejados e torna as informações
     * acessiveis para o sistema.
     *
     * @var object Vita\Core\Request
     */
    protected $_request = null;

    /**
     * Unique Instance
     *
     * @var Vita
     */
    private static $instance;

    /**
     * Objeto Mail. Representa a instancia do PHPMailer
     *
     * @var object - PHPMailer
     **/
    public $_mail = null;

    /**
     * No Construct allowed
     */
    private function __construct()
    {
    }

    /**
     * No Clone allowed
     */    
    private function __clone()
    {
    }
    
    /**
     * No Wakeup allowed
     */
    private function __wakeup()
    {
    }

    /**
     * Inicializa todos os Servicos oferecidos pela Biblioteca Vita
     *
     * @return void
     */
    public function init(Config $config)
    {
        $this
            ->_loadConfig($config)
            ->_loadLog()
            ->_loadSession()
            ->_loadValidade()
            ->_loadSanitize()
            ->_loadUtils()
            ->_loadUploadManager()
            ->_loadDefaultTimezone()
            ->_loadDataManager()
            ->_loadSqlite()
            ->_loadMailerService()
            ->_loadRequest()
            ->_loadResponse()
            ->_loadRenderer();
        
        return $this;
    }

    /**
     * Load Config Object Manager
     *
     * @param Config $config
     *
     * @return $this
     */
    private function _loadConfig(Config $config = null)
    {
        if (!is_null($config)) {
            $this->_config = $config;
            return $this;
        }

        $repository = new \Vita\Core\Config\ConfigRepository();
        $this->_config = new \Vita\Core\Config\Config($repository);

        return $this;
    }

    /**
     * Get Config Manager
     *
     * @return Vita\Core\Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Load Log Manager System
     *
     * @return $this
     */
    private function _loadLog()
    {
        $this->_log = new \Vita\Core\Log\Log(
            $this->_config->vita_path . $this->_config->log_folder
        );
        return $this;
    }

    /**
     * Loads PHP Session Manager
     *
     * @return Vita
     */
    private function _loadSession()
    {
        $expire = $this->getConfig()->get('session_expire_time', 900);
        $this->setSession(new Session($expire));
        return $this;
    }

    /**
     * Load Validation Manager Object into this instance
     *
     * @return $this
     */
    private function _loadValidade()
    {
        $this->_validate = new \Vita\Core\Validate\Validate();
        return $this;
    }

    /**
     * Returns Validate Object
     *
     * @return type
     */
    public function getValidate()
    {
        return $this->_validate;
    }
    
    /**
     * Load Sanitize Manager Object into this instance
     *
     * @return $this
     */
    private function _loadSanitize()
    {
        $this->_sanitize = new \Vita\Core\Validate\Sanitize();
        return $this;
    }

    /**
     * Returns Sanitize Object
     *
     * @return type
     */
    public function getSanitize()
    {
        return $this->_sanitize;
    }
    
    /**
     * Load Utils Function Object
     *
     * @return $this
     */
    private function _loadUtils()
    {
        $this->_utils = new \Vita\Core\Utils();
        return $this;
    }

    /**
     * Returns Utils Object
     *
     * @return type
     */
    public function getUtils()
    {
        return $this->_utils;
    }
    
    /**
     * Load Request Object
     *
     * @return $this
     */
    private function _loadRequest()
    {
        $this->_request = new \Vita\Core\Request(false);
        $this->_request->init();
        return $this;
    }

    /**
     * Returns Request Object
     *
     * @return type
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    /**
     * Load Response Object
     *
     * @return $this
     */
    private function _loadResponse()
    {
        $this->_response = new \Vita\Core\Response();
        //$this->_response->init();
        return $this;
    }

    /**
     * Returns Response Object
     *
     * @return type
     */
    public function getResponse()
    {
        return $this->_response;
    }
    
    /**
     * Load Renderer Object
     *
     * @return $this
     */
    private function _loadRenderer()
    {
        $path = $this->getViewFolder();

        if (!is_dir($path)) {
            throw new \Exception("Attempting to start the template management system in a nonexistent folder: '{$path}'");
        }

        require_once __DIR__ . '/Library/Twig/Autoloader.php';
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem($path);

        # verificando se o modo de cache esta liberado no arquivo de config
        $cacheFolder = ($this->getConfig()->twig_cache_enable === true) ? $this->getConfig()->system_path . 'cache' . DIRECTORY_SEPARATOR : false;

        # instanciando o ambiente twig
        $twig = new \Twig_Environment($loader, 
            array('cache' => $cacheFolder, 'debug' => $this->getConfig()->twig_debug_enable)
        );

        # adiciona extensao para realizar debug
        if($this->getConfig()->twig_debug_enable) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }

        # adicionando nosso filtro proprio dde traducoes
        $twig->addFilter('vtrans', new \Twig_Filter_Function('vita_twig_translate_filter'));

        $this->_renderer = new \Vita\Core\Renderer($twig);
        return $this;
    }

    /**
     * Returns Renderer Object
     *
     * @return type
     */
    public function getRenderer()
    {
        return $this->_renderer;
    }
    
    /**
     * Set Session Manager
     *
     * @param SessionInterface $session
     *
     * @return Vita
     */
    public function setSession(SessionInterface $session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     * Returns Session Management Object
     *
     * @return type
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Retorna caminho para pasta com templates/view
     *
     * @return String
     */
    public function getViewFolder()
    {
        $cfg = $this->getConfig();
        return "{$cfg->app_folder}{$cfg->view_folder}" . DIRECTORY_SEPARATOR;
    }

    /**
     * Loads File Upload Manager
     *
     * @return $this
     */
    private function _loadUploadManager()
    {
        $this->_upload = new \Vita\Core\Upload();
        $uploadConfig = [
            'destination'    => $this->getConfig()->get('upload_folder'),
            'overrideFile'   => false,
            'randomFileName' => false,
            'maxsize'        => $this->getConfig()->get('max_file_size'),
            'max_imgWidth'   => $this->getConfig()->get('max_img_width'),
            'max_imgHeight'  => $this->getConfig()->get('max_img_height'),
            'printErrors'    => false,
        ];
        $this->_upload->init($uploadConfig);
        return $this;
    }

    /**
     * Set Default Timezone for the System
     *
     * @return $this
     */
    private function _loadDefaultTimezone()
    {
        $timezone = $this->getConfig()->get('default_time_zone', 'Brazil/East');
        date_default_timezone_set($timezone);

        return $this;
    }

    /**
     * Carrega o Gerenciador de Entidades Virtuais
     *
     * @access private
     * 
     * @return \Vita
     * 
     * @throws \Exception
     */
    private function _loadDataManager()
    {
        if ($this->getConfig()->get('load_data_manager')) {

            $drive = new \Wsantana\VeManager\Database\Provider\Mysql();
            $drive
                ->setHostname($this->getConfig()->get('dbhost'))
                ->setPort($this->getConfig()->get('dbport'))
                ->setUsername($this->getConfig()->get('dbuser'))
                ->setPassword($this->getConfig()->get('dbpass'))
                ->setDatabaseName($this->getConfig()->get('dbname'));

            $this->getConfig()->delete('dbuser');
            $this->getConfig()->delete('dbpass');

            $connection = $drive->conectar();
            $this->_entityManager = new \Wsantana\VeManager\VeManager($connection);
        }
        
        return $this;
    }

    /**
     * Returns Entity Manager Object
     *
     * @return VeManager
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    /**
     * Return a New Query Builder
     *
     * @access public
     * 
     * @static
     * 
     * @return \Wsantana\VeManager\QueryBuilder
     */
    public static function getQueryBuilder()
    {
        return new \Wsantana\VeManager\QueryBuilder();
    }

    private function _loadSqlite()
    {
        if ($this->_config->load_sqlite) {
            $conexaoDadossqlite_ = array(
                'dbpath' => $this->_config->sqlite_folder,
                'dbname' => $this->_config->sqlite_dbname
            );
            $this->sqlite = SystemCoreDatabaseFactory::create( 'SQLite', $conexaoDadossqlite_ );
        }
        return $this;
    }

    /**
     * Loads Email sending system
     *
     * @return \Vita
     */
    private function _loadMailerService()
    {
        require_once __DIR__ . '/Library/phpmailer/PHPMailerAutoload.php';
        $this->_mail = new \PHPMailer;
        return $this;
    }

    /**
     * Returns Mailer Service Management Object
     *
     * @return PHPMailer
     */
    public function getMail()
    {
        return $this->_mail;
    }
    
    /**
     * Returns Global Setted Variables for de View
     *
     * @return Array
     */
    public function getGlobalVars()
    {   
        return array(
            'base_url' => $this->getConfig()->get('url')
        );
    }

    /**
     * Returns Unique Vita Instance
     *
     * @return Vita
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Magic Method GET
     *
     * @param type $key
     *
     * @return type
     */
    public function __get($key)
    {
        $objectVars = (array_keys(get_object_vars($this)));
        $property = "_{$key}";
        if (in_array($property, $objectVars)) {
            return $this->$property;
        }
        return null;
    }
}
