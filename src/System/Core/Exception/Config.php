<?php

#    tenta pegar informacoes de configuracoes externas a
#    este arquivp, mas...
global $config;


#    Exception nao pode depender de um informacoes externas
#    logo, deve haver uma garantia grande que essa classe
#    funcione de forma independente de qualquer outra.
$eConfig = array();

$eConfig['default_time_zone'] = isset($config['default_time_zone'])
                              ? $config['default_time_zone']
                              : 'Brazil/East';

$eConfig['date_format'] = isset($config['date_format'])
                        ?  $config['date_format']
                        : 'Y-m-d H:i:s';

$eConfig['sys_error_log_type'] = isset($config['sys_error_log_type'])
                               ? $config['sys_error_log_type']
                               : 3;

$eConfig['sys_errorfile_destination'] = isset($config['sys_errorfile_destination'])
                                      ? $config['sys_errorfile_destination']
                                      : 'sys_erros.dat';

$eConfig['log_folder'] = isset($config['log_folder'])
                       ? VITA_PATH . $config['log_folder']
                       : SYS_PATH  . 'log' . DIRECTORY_SEPARATOR;

$eConfig['sys_error_log_email'] = isset($config['sys_error_log_email'])
                                ?  $config['sys_error_log_email']
                                : 'seu.email.dev@seudominio.com.br';

$eConfig['show_error_log'] = isset($config['show_error_log'])
                           ? $config['show_error_log']
                           : true;

$eConfig['show_extra_log'] = isset($config['show_extra_log'])
                           ?  $config['show_extra_log']
                           : true;

#    Extra Log
#    caso uma informacao seja setada
#    neste array, sera exibido ao final do log
$eConfig['elog'] = array();


/**
 *    Funcao para uso Debug
 *    Basicamente, insere informacoes
 *    a serem exibidas na tela de erro,
 *    alem das rotineiras.
 */
function sysExceptionDebug($info = null)
{
    global $eConfig;
    $eConfig['elog'][] = $info;
}