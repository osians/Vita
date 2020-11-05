<?php


namespace Vita;

use Exception;
use Osians\VeManager\Database\Provider\Mysql;
use Osians\VeManager\QueryBuilder;
use Osians\VeManager\VeManager;
use PHPMailer;
use Vita\Core\Config\Config;
use Vita\Core\Config\ConfigRepository;
use Vita\Core\Log\Log;
use Vita\Core\Renderer;
use Vita\Core\Request;
use Vita\Core\Response;
use Vita\Core\Session\Session;
use Vita\Core\Session\SessionInterface;
use Vita\Core\Upload;
use Vita\Core\Utils;
use Vita\Core\Validate\Sanitize;
use Vita\Core\Validate\Validate;

/**
 * Class VitaService
 *
 * Vita load Service Class
 *
 * @package Vita
 */
class VitaService
{
    /**
     * Config System Manager
     *
     * @var Config $_config
     */
    protected $_config = null;

    /**
     * Log info data into Files
     *
     * @var object Vita\Core\Log
     */
    protected $_log = null;

    /**
     * PHP Session Manager
     *
     * @var SessionInterface
     */
    protected $_session = null;

    /**
     * Data Validate
     *
     * @var Validate $_validate
     */
    protected $_validate = null;

    /**
     * Data Sanitize
     *
     * @var Sanitize $_sanitize
     */
    protected $_sanitize = null;

    /**
     * File Upload Object
     *
     * @var Upload
     */
    protected $_upload = null;

    /**
     * Gerencia acesso a database
     *
     * @var VeManager $_entityManager
     */
    protected $_entityManager = null;

    /**
     * Utils
     *
     * @var Utils
     */
    protected $_utils = null;

    /**
     * Quando um Formulário emite um POST, este
     * Objeto trata o Post, limpa os dados de
     * caracteres indesejados e torna as informações
     * acessíveis para o sistema.
     *
     * @var Request $_request
     */
    protected $_request = null;

    /**
     * @var Response $_response
     */
    protected $_response = null;

    /**
     * @var Renderer $_renderer
     */
    protected $_renderer = null;

    /**
     * Objeto Mail. Representa a instancia do PHPMailer
     *
     * @var PHPMailer
     **/
    public $_mail = null;

    /**
     * Inicializa todos os serviços oferecidos pela Biblioteca Vita
     *
     * @param Config $config
     * @return $this
     * @throws Exception
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
    protected function _loadConfig(Config $config = null)
    {
        if (!is_null($config)) {
            $this->_config = $config;
            return $this;
        }

        $repository = new ConfigRepository();
        $this->_config = new Config($repository);

        return $this;
    }

    /**
     * Load Log Manager System
     *
     * @return $this
     */
    protected function _loadLog()
    {
        $config = $this->getConfig();

        $this->_log = new Log(
            $config->get('vita_path') . $config->get('log_folder')
        );

        return $this;
    }

    /**
     * Loads PHP Session Manager
     *
     * @return $this
     */
    protected function _loadSession()
    {
        $expire = $this->getConfig()->get('session_expire_time', 900);
        $this->_session = new Session($expire);
        return $this;
    }

    /**
     * Load Validation Manager Object into this instance
     *
     * @return $this
     */
    protected function _loadValidade()
    {
        $this->_validate = new Validate();
        return $this;
    }

    /**
     * Load Sanitize Manager Object into this instance
     *
     * @return $this
     */
    protected function _loadSanitize()
    {
        $this->_sanitize = new Sanitize();
        return $this;
    }

    /**
     * Load Utils Function Object
     *
     * @return $this
     */
    protected function _loadUtils()
    {
        $this->_utils = new Utils();
        return $this;
    }

    /**
     * Loads File Upload Manager
     *
     * @return $this
     */
    protected function _loadUploadManager()
    {
        $this->_upload = new Upload();
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
    protected function _loadDefaultTimezone()
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
     * @return $this
     *
     * @throws Exception
     */
    protected function _loadDataManager()
    {
        if ($this->getConfig()->get('load_data_manager')) {

            $drive = new Mysql();
            $drive
                ->setHostname($this->getConfig()->get('dbhost'))
                ->setPort($this->getConfig()->get('dbport'))
                ->setUsername($this->getConfig()->get('dbuser'))
                ->setPassword($this->getConfig()->get('dbpass'))
                ->setDatabaseName($this->getConfig()->get('dbname'));

            $this->getConfig()->set('dbuser', null);
            $this->getConfig()->set('dbpass', null);

            $connection = $drive->connect();
            $this->_entityManager = new VeManager($connection);
        }

        return $this;
    }

    /**
     * Loads Email sending system
     *
     * @return $this
     */
    protected function _loadMailerService()
    {
        require_once __DIR__ . '/Library/phpmailer/PHPMailerAutoload.php';
        $this->_mail = new PHPMailer;
        return $this;
    }

    /**
     * Load Request Object
     *
     * @return $this
     */
    protected function _loadRequest()
    {
        $this->_request = new Request();
        $this->_request->init();
        return $this;
    }


    /**
     * Load Response Object
     *
     * @return $this
     */
    protected function _loadResponse()
    {
        $this->_response = new Response();
        $this->_response->init();
        return $this;
    }

    /**
     * Load Renderer Object
     *
     * @return $this
     * @throws Exception
     */
    protected function _loadRenderer()
    {
        // TODO - Remover a livraria TWIG
        $path = $this->getViewFolder();

        if (!is_dir($path)) {
            throw new Exception("Attempting to start the template management system in a nonexistent folder: '{$path}'");
        }

        require_once __DIR__ . '/Library/Twig/Autoloader.php';
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem($path);

        # verificando se o modo de cache esta liberado no arquivo de config
        $cacheFolder = ($this->getConfig()->get('twig_cache_enable') === true) ? $this->getConfig()->get('system_path') . 'cache' . DIRECTORY_SEPARATOR : false;

        # instanciando o ambiente twig
        $twig = new \Twig_Environment($loader,
            array('cache' => $cacheFolder, 'debug' => $this->getConfig()->get('twig_debug_enable'))
        );

        # adiciona extensão para realizar debug
        if($this->getConfig()->get('twig_debug_enable')) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }

        # adicionando nosso filtro próprio dde traduções
        $twig->addFilter('vtrans', new \Twig_Filter_Function('vita_twig_translate_filter'));

        $this->_renderer = new Renderer($twig);
        return $this;
    }

    /**
     * Get Config Manager
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Retorna caminho para pasta com templates/view
     *
     * @return String
     */
    public function getViewFolder()
    {
        $config = $this->getConfig();
        return "{$config->get('app_folder')}{$config->get('view_folder')}" . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns Validate Object
     *
     * @return Validate
     */
    public function getValidate()
    {
        return $this->_validate;
    }

    /**
     * Returns Sanitize Object
     *
     * @return Sanitize
     */
    public function getSanitize()
    {
        return $this->_sanitize;
    }

    /**
     * Returns Utils Object
     *
     * @return Utils
     */
    public function getUtils()
    {
        return $this->_utils;
    }

    /**
     * Returns Request Object
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Returns Response Object
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Returns Renderer Object
     *
     * @return Renderer
     */
    public function getRenderer()
    {
        return $this->_renderer;
    }

    /**
     * Returns Session Management Object
     *
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->_session;
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
     * @return QueryBuilder
     */
    public static function getQueryBuilder()
    {
        return new QueryBuilder();
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
}