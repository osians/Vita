<?php

/*
 *---------------------------------------------------------------
 * PADRONIZANDO TUDO EM UTF8
 *---------------------------------------------------------------
 * adotando o tipo de codificacao UTF-8 para 
 * todos os processos do sistema, afim de 
 * evitar erros de caracteres.
 */
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

if (function_exists('mb_http_output')) {
    mb_http_output('UTF-8');
}

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
switch (ENVIRONMENT)
{
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
    break;

    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        if (version_compare(PHP_VERSION, '5.3', '>=')) {
                error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        } else {
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        }
    break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1); // EXIT_ERROR
}

/*
 *---------------------------------------------------------------
 * SYSTEM DIRECTORY NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" directory.
 * Set the path if it is not in the same directory as this file.
 */
$systemFolder = 'System';


$systemPath = (($tmp = realpath($systemFolder)) !== false)
    ? $tmp . DIRECTORY_SEPARATOR
    : dirname(__FILE__) . DIRECTORY_SEPARATOR . $systemFolder . DIRECTORY_SEPARATOR;

# encontramos o caminho para o sistema?
if (!is_dir($systemPath)) {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'A Pasta do Sistema não parece ter sido setada corretamente. Por favor, abra o seguinte arquivo para corrigir isso: ' . pathinfo(__FILE__, PATHINFO_BASENAME);
    exit(3); // EXIT_CONFIG
}


/*
 *---------------------------------------------------------------
 * CONFIG
 *---------------------------------------------------------------
 * 
 * O Vita pode ser compartilhado por varios sites ou sistemas 
 * ao mesmo tempo. Portanto, diferentes sistemas podem usa-lo 
 * com diferentes configuracoes.
 * Nota: Caso uma variavel chamada $config com configuracoes 
 * validas para o Vita seja setada antes de chamar este arquivo, 
 * ela tera prioridade e sera usada.
 */
if (isset($config)) {
    $clientConfig = $config;
    unset($config);
}

$configDirectory = $systemPath . 'Core' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR;
require_once $configDirectory  . 'ConfigRepositoryInterface.php';
require_once $configDirectory  . 'ConfigRepository.php';
require_once $configDirectory  . 'Config.php';

$repository = new Vita\Core\Config\ConfigRepository();
$config = new Vita\Core\Config\Config($repository);

# ------------------------------------------
# inserindo configuracoes externas, caso existam,
# e sobrescrevendo as padroes
if (isset($clientConfig) && is_array($clientConfig)) {
	foreach ($clientConfig as $key => $value) {
		$config->set($key, $value);
	}
}

# reconfigurando report de erros
# com base nos dados do arquivo de configuracao
if ($config->get('vita_dev_mode') == false) {
	ini_set('display_errors', 0);
	if (version_compare(PHP_VERSION, '5.3', '>=')) {
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
	}
	else {
		error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
	}
}

# ------------------------------------------
# Setando alguns caminhos importantes do sistema
$config->vita_path     = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$config->system_path   = $systemPath;
$config->config_path   = $configDirectory;
$config->core_path     = $systemPath . 'Core'    . DIRECTORY_SEPARATOR;
$config->helper_path   = $systemPath . 'Helpers' . DIRECTORY_SEPARATOR;
$t = debug_backtrace();
$config->app_folder    = isset($t[0])?dirname($t[0]['file']).DIRECTORY_SEPARATOR : $config->vita_path;

# ------------------------------------------
# inicianlizando nosso sistema tratamento de erros
# que ira tratar os problemas e apresentar de uma
# maneira mais amigavel qualquer erro que acontecer no sistema!
// require_once($config->core_path . 'sys_exception.class.php' );

# ------------------------------------------
# iniciando funcoes Modulares basicas do sistema
# Caso haja uma funcao util modular, colocar aqui
require_once($config->helper_path . 'helper.php');

# ------------------------------------------
# inicializa o sistema vita que
# ira gerenciar o acesso e uso de
# todos os recursos do sistema, alem de
# oferecer facilidades para a implementacao
# de softwares.
require_once $systemPath . 'Vita.php';

use \Vita\Vita;

# ------------------------------------------
# dando vida ao sistema
$vita = Vita::getInstance();
$vita->init($config);
unset($config);

# ------------------------------------------
# daqui em diante, o sistema todo e' gerido
# atraves de objetos, e pode ser acessado por:
# $vita ou Vita::getInstance().
#
# logo, para obter uma configuração setada no
# arquivo config.php por exemplo, pode-se usar:
# $vita->config->dbname;
# $vita->config->dbname; (ou $vita->config->get( 'dbname' );)
# $vita->config->dbname;
# System::getInstance()->config->dbname;


# tentando resumir informacoes uteis em aliases
$vita->base_url = $vita->config->url;
$vita->request_uri = uri();

$vita->config->template_url = $vita->config->url . $vita->config->view_folder . "/";

# roteando url
require_once $vita->config->system_path . 'Core/Router/Router.php' ;
require_once $vita->config->system_path . 'Core/Router/RouterStatus.php';
use \Vita\Core\Router\Router;
$controlFolder = $vita->config->app_folder . $vita->config->controller_folder . DIRECTORY_SEPARATOR;
$request = isset($_GET['request']) ? $_GET['request'] : null;
$r = new Router($controlFolder, $request);
$r->setInput($vita->getRequest())
  ->setResponse($vita->getResponse())
  ->setRenderer($vita->getRenderer());
$r->rotear();
die();
