<?php

define('VITA_START_LOAD', microtime(true));

/*
 *---------------------------------------------------------------
 * PADRONIZANDO TUDO EM UTF8
 *---------------------------------------------------------------
 * adotando o tipo de codificação UTF-8 para
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
switch (ENVIRONMENT) {
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
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
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
$systemPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $systemFolder . DIRECTORY_SEPARATOR;

# encontramos o caminho para o sistema?
if (!is_dir($systemPath)) {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'A Pasta do Sistema não parece ter sido setada corretamente. 
          Por favor, abra o seguinte arquivo para corrigir isso: ' .
          pathinfo(__FILE__, PATHINFO_BASENAME);
    exit(3); // EXIT_CONFIG
}


/*
 *---------------------------------------------------------------
 * CONFIG
 *---------------------------------------------------------------
 *
 * O Vita pode ser compartilhado por vários sites ou sistemas
 * ao mesmo tempo. Portanto, diferentes sistemas podem usa-lo
 * com diferentes configurações.
 * Nota: Caso uma variável chamada $config com configurações
 * validas para o Vita seja setada antes de chamar este arquivo,
 * ela tera prioridade e sera usada.
 */
if (isset($config)) {
    $clientConfig = $config;
    unset($config);
}

$configDirectory = $systemPath . 'Core' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR;
require_once $configDirectory . 'ConfigRepositoryInterface.php';
require_once $configDirectory . 'ConfigRepository.php';
require_once $configDirectory . 'Config.php';

use System\Core\Config\Config;
use System\Core\Config\ConfigRepository;

$repository = new ConfigRepository();
$config = new Config($repository);

# ------------------------------------------
# inserindo configurações externas, caso existam,
# e sobrescrevendo as padrões
if (isset($clientConfig) && is_array($clientConfig)) {
    foreach ($clientConfig as $key => $value) {
        $config->set($key, $value);
    }
}

/**
 * ---------------------------------------------------------------
 * Guardando alguns caminhos importantes do sistema
 * ---------------------------------------------------------------
 */
$config->set('vita_path', dirname(__FILE__) . DIRECTORY_SEPARATOR);
$config->set('system_path', $systemPath);
$config->set('config_path', $configDirectory);
$config->set('core_path', $systemPath . 'Core' . DIRECTORY_SEPARATOR);
$config->set('helper_path', $systemPath . 'Helpers' . DIRECTORY_SEPARATOR);
$t = debug_backtrace();
$appFolder = isset($t[0]) ? dirname($t[0]['file']) . DIRECTORY_SEPARATOR : $config->get('vita_path');
$config->set('app_folder', $appFolder);

/**
 * ---------------------------------------------------------------
 * HELPERS
 * ---------------------------------------------------------------
 * iniciando funções Modulares básicas do sistema
 * Caso haja uma função util modular, colocar no arquivo Helper
 *
 */
require_once($systemPath . 'Helpers' . DIRECTORY_SEPARATOR . 'Helper.php');

/**
 * ---------------------------------------------------------------
 * AUTOLOADER
 * ---------------------------------------------------------------
 */
require_once $systemPath . 'Autoloader.php';

# ------------------------------------------
# inicializa o sistema vita que
# ira gerenciar o acesso e uso de
# todos os recursos do sistema, além de
# oferecer facilidades para a implementação
# de softwares.
require_once $systemPath . 'VitaService.php';
require_once $systemPath . 'Vita.php';

use System\Core\Router\RouterClassException;
use \Vita\Vita;

/**
 * ---------------------------------------------------------------
 *  INICIANDO O VITA
 * ---------------------------------------------------------------
 */
$vita = Vita::getInstance();

try {
    $vita->init($config);
} catch (Exception $e) {
    die($e->getMessage());
}

unset($config);

$vita->getConfig()->set(
    'template_url',
    $vita->getConfig()->get('url') . $vita->getConfig()->get('view_folder') . "/"
);

/**
 * ---------------------------------------------------------------
 *  INICIANDO O ROTEAMENTO
 * ---------------------------------------------------------------
 */
require_once $systemPath . 'Core/Router/Router.php';
require_once $systemPath . 'Core/Router/RouterStatus.php';

use System\Core\Router\Router;

if (in_array('mod_rewrite', apache_get_modules()) === false) {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'Erro:  mod_rewrite ausente no servidor.';
    exit(3); // EXIT_CONFIG
}

$controlFolder = $vita->getConfig()->get('app_folder') .
                 $vita->getConfig()->get('controller_folder') . DIRECTORY_SEPARATOR;
$dataFolder = $vita->getConfig()->get('app_folder') . 'data' . DIRECTORY_SEPARATOR;

Autoloader::getInstance()->addFolder($controlFolder);
Autoloader::getInstance()->addFolder($dataFolder);

$request = isset($_GET['request']) ? $_GET['request'] : null;

try {
    $r = new Router($controlFolder, $request);

    $vita->setRouter($r);

    $r->setInput($vita->getRequest())
        ->setResponse($vita->getResponse())
        ->setRenderer($vita->getRenderer());

    define('VITA_STOP_LOAD', microtime(true));

    $r->init();
} catch (RouterClassException $e) {
    echo $e->getMessage();
    exit(1);
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}

exit(0);
