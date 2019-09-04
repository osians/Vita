<?php

#-------------------------------------------
# padronizando tudo em utf8
# ------------------------------------------
# adotando o tipo de codificacao UTF-8 para
# todos os processos do sistema, afim de
# evitar erros de caracteres.
# ------------------------------------------
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

if (function_exists('mb_http_output')) {
    mb_http_output('UTF-8');
}

header('Content-Type: text/html; charset=utf-8');

define('DS', DIRECTORY_SEPARATOR);

# ------------------------------------------
# Configurando o ambiente
# reconhecendo o ambiente em que o sistema se encontra
# e tentando iniciar as funcoes basicas e necessarias
# para o funcionamento.
# ------------------------------------------

# reportando erros inicial
error_reporting(-1);
ini_set('display_errors', 1);

# ------------------------------------------
# nome da pasta que guarda o sistema
$systemFolder = 'System';

# verificando se a pasta do sistema
# realmente existe, e se a encontramos
if (($tmp = realpath($systemFolder)) !== false) {
    $systemPath = $tmp . DS;
} else {
    $systemPath = dirname(__FILE__)
                . DS
                . $systemFolder
                . DS;
}

# encontramos o caminho para o sistema?
if (!is_dir($systemPath)) {
    die("Pasta do Sistema Vita não encontrada em: '".$systemPath."'");
}

# ------------------------------------------
# Decidindo qual arquivo de configuracoes usar
# ------------------------------------------
# O Vita pode ser compartilhado por varios sites ou sistemas
# ao mesmo tempo. Portanto, diferentes sistemas podem usa-lo
# com diferentes configuracoes.
# Nota: Caso uma variavel chamada $config com configuracoes
# validas para o Vita seja setada antes de chamar este arquivo,
# ela tera prioridade e sera usada.
if (isset($config)) {
    $clientConfig = $config;
    unset($config);
}

# ------------------------------------------
# indentificando arquivo de configuracoes basicas do sistema
$configFile = dirname(__FILE__) . DS . 'Config' . DS . 'config.php';

if (!file_exists($configFile)) {
    die(
        "Arquivo de configurações básicas do sistema não encontrado na pasta: '{$configFile}'"
    );
}

# ------------------------------------------
# incluindo arquivo que guarda array com configuracoes
# basicas do sistema. As configuracoes serao inseridas
# temporariamente dentro da variavel $config[...].
# A variavel $config sera usado apenas nesse arquivo e
# resetado ao final. Para usar as informacoes de configuracoes
# sera necessario usar a instancia principal do vita.
require_once($configFile);

# ------------------------------------------
# inserindo configuracoes externas, caso existam,
# e sobrescrevendo as padroes
if (isset($clientConfig) && is_array($clientConfig)) {
    foreach ($clientConfig as $key => $value) {
        $config[$key] = $value;
    }
}

#    reconfigurando report de erros
#    com base nos dados do arquivo de configuracao
if (!$config['vita_dev_mode']) {
    ini_set('display_errors', 0);
    if (version_compare(PHP_VERSION, '5.3', '>=')) {
        error_reporting(
            E_ALL
            & ~E_NOTICE
            & ~E_DEPRECATED
            & ~E_STRICT
            & ~E_USER_NOTICE
            & ~E_USER_DEPRECATED
        );
    } else {
        error_reporting(
            E_ALL
            & ~E_NOTICE
            & ~E_STRICT
            & ~E_USER_NOTICE
        );
    }
}


#    Setando alguns caminhos importantes do sistema
$config['vita_path'] = dirname(__FILE__) . DS;

$config['system_path'] = $systemPath;
$config['config_path'] = $config['vita_path'] .'Config' . DS;
$config['core_path']   = $systemPath . 'Core'    . DS;
$config['helper_path'] = $systemPath . 'Helpers' . DS;

$t = debug_backtrace();

#    arquivo que chamos o vita
$config['app_folder'] = isset($t[0])
                      ? dirname($t[0]['file']).DS
                      : $config['vita_path'];

# ------------------------------------------
# Uma vez que o array $config deixara de existir,
# quando o sistema vita iniciar,
# setamos infos importantes imutaveis como constantes.
define('VITA_PATH', $config['vita_path']);
define('CORE_PATH', $config['core_path']);
define('SYS_PATH',  $config['system_path']);


# ------------------------------------------
# inicianlizando nosso sistema tratamento de erros
# que ira tratar os problemas e apresentar de uma
# maneira mais amigavel qualquer erro que acontecer no sistema!
require_once($config['core_path'] . 'Exception' . DS . 'VitaException.php');


# ------------------------------------------
# iniciando funcoes Modulares basicas do sistema
# Caso haja uma funcao util modular, colocar nesse arquivo.
require_once($config['helper_path'] . 'Helper.php');


# ------------------------------------------
# inicializa o sistema vita que
# ira gerenciar o acesso e uso de
# todos os recursos do sistema, alem de
# oferecer facilidades para a implementacao
# de softwares.
require_once $systemPath . 'vita.php';


use Vita\System\Vita;

# ------------------------------------------
# dando vida ao sistema
$vita = Vita::getInstance();
$vita->init($config);

unset($config);

# ------------------------------------------
# daqui em diante, o sistema todo e' gerido
# atraves de objetos, e pode ser acessado por:
# vita(), vita() ou System::getInstance().
#
# logo, para obter uma configuração setada no
# arquivo config.php por exemplo, pode-se usar:
# $vita->config->dbname;
# vita()->config->dbname; (ou vita()->config->get( 'dbname' );)
# vita()->config->dbname;
# System::getInstance()->config->dbname;

# verifica se esta definido para parar aqui e retornar
# para um sistema externo. define( 'VITACON', TRUE );
if (VITAONLY == false && isset($t[0])) {
    # tentando resumir informacoes uteis em aliases
    vita()->view_folder = vita()->config->app_folder
                        . vita()->config->view_folder
                        . DS;
    vita()->base_url = vita()->config->url;
    vita()->request_uri = uri();

    // inicialize o sistema de templates. Os templates se
    // encontram nesta pasta.
    vita()->init_tpl_system(vita()->view_folder);
    vita()->config->template_url = vita()->config->url
                                 . vita()->config->view_folder
                                 . "/";

    # roteando url
    require_once vita()->config->system_path . 'router.php';
    $r = new Router();
    $r->router();
}

if (!isset($t[0])) {
    print "vita_version: " . Vita::VERSION . "<br>";
}
