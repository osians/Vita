<?php

namespace Framework\Vita\Core;

use Framework\Vita\Vita;

header('Content-Type: text/html; charset=utf-8');

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

/**
 *    Classe responsavel por apresentar uma Pagina
 *    HTML Amigavel contendo o erro encontrado.
 */
class ErrorOutput
{
    public function __set($name, $value)
    {
        $this->$name = htmlspecialchars($value);
    }

    public function __toString()
    {
        global $eConfig;

        if (ob_get_contents()) {
            ob_end_clean();
        }

        date_default_timezone_set($eConfig['default_time_zone']);
        $data_formatada = date($eConfig['date_format'], $_SERVER['REQUEST_TIME']);
        $trace = str_replace("#", "<br>#", $this->traceAsString);
        $arquivo = basename($this->file);
        $fullRequestUri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        $this->trechoComErro = "";

        $dbp = (isset(Vita::getInstance()->config->dbpass)) ? Vita::getInstance()->config->dbpass : false;
        $dbu = (isset(Vita::getInstance()->config->dbuser)) ? Vita::getInstance()->config->dbuser : false;
        if( $dbp ) {
            $this->message = str_replace( $dbp, '*************', $this->message );
            $this->message = str_replace( $dbu, '*************', $this->message );
        }

        // obtendo codigo do arquivo que deu erro
        $handle = fopen($this->file, "r");

        if ($handle) {
            
            $linhaAtual   = 1;
            $inicioTrecho = $this->line - 3;
            $finalTrecho  = $this->line + 2;

            while (($line = fgets($handle)) !== false){
                // obtem apenas 3 linhas de código
                if($linhaAtual >= $inicioTrecho){
                    if($linhaAtual == $this->line) $this->trechoComErro .= "<b>";
                    $this->trechoComErro .= "{$linhaAtual}: {$line}";
                    if($linhaAtual == $this->line)
                        $this->trechoComErro .= "</b>";
                    $this->trechoComErro .= "<br>";

                    if($linhaAtual == $finalTrecho) break;
                }
                $linhaAtual++;
            }
            fclose($handle);
        } else {
            // error opening the file.
        }

        // checa se a dump extra dentro de elog.
        $elog = (!empty($eConfig['elog'])) ? implode( "<br>", $eConfig['elog'] ) : null;
        $elog = (null == $elog) ? "" : '<span class="elog"><b>Extra Log:</b> <br>'. $elog . '</span><br><br>';

        // verificando se devemos apresentar o extra log
        $show_extra_log = ($eConfig['show_extra_log'] == FALSE) ? "display:none;" : "";

        // verificando se deve apresentar uma versao mais light de erros
        if($this->show_error_log)
        {

        $html =<<<HTML
        <html>
            <head>
                <title>Vita: Erro Capturado</title>
                <style>
                    html,body{margin:0;padding:0;}
                    body{
                        background-color:#28282E;
                        color:#C6E863;
                    }
                    div.container{
                        margin-left:40px;
                    }
                    h1{
                       font-family:"Open Sans",Verdana, Sans-serif;
                       font-size:36px;
                       display:block;
                       color:#FF6A44;
                       font-weight:100;
                    }
                    a{
                        background-color: #C6E863;
                        color: #28282E;
                        padding: 15px 20px;
                        text-decoration: none;
                        cursor: pointer;
                        margin-bottom: 50px;
                        border-radius: 4px;
                    }
                    a:hover{
                        opacity: .8;
                    }
                    div.errwrapper{
                        font-size:16px;
                        font-family:monospace;
                    }
                    div.errmais{
                        color:#9AA2A9;
                        border-top: 1px dashed;
                        border-bottom: 1px dashed;
                        padding-top: 30px;
                        margin-bottom: 30px;
                    }
                    span.trecho_code_err{
                        background-color: #28282E;
                        display: block;
                        margin:15px 0;
                    }
                    span.elog{
                        color:#3F8AFF;
                    }
                    span.trecho_code_err b{
                        color:#FFFF00;
                    }
                    span.show_extra_log{
                        {$show_extra_log}
                    }
                    span.msg_errro{color:#DD982E;background-color:#202024;display:block;padding: 10px 25px;}
                </style>
            </head>
            <body>
            <div class="container">
                <br>
                <h1>Erro em: {$arquivo}</h1>

                <div class="errwrapper">
                    <div style="">
                        <span class="msg_errro">
                            {$this->message}
                        </span>
                        <span class='trecho_code_err'>
                            {$this->trechoComErro}
                        </span>
                        <br>

                        {$elog}

                        <div class="errmais">

                            <b>Mensagem:</b>   {$this->message} <br>
                            <b>Arquivo</b>:    {$this->file} (Linha: {$this->line}) <br>
                            <b>Código</b>:     {$this->code} <br>
                            <b>Trace(str):</b> {$trace} <br>
                            <br><br>

                            <span class='show_extra_log'>
                            <b>Dados adicionais</b> <br>
                            REQUEST_URI: {$fullRequestUri}<br>
                            HTTP_ACCEPT:  {$_SERVER['HTTP_ACCEPT']} <br>
                            USER_AGENT :  {$_SERVER['HTTP_USER_AGENT']} <br>
                            REMOTE_ADDR:  {$_SERVER['REMOTE_ADDR']} <br>
                            REQUEST_TIME: {$data_formatada}<br>
                            SERVER_ADMIN: {$_SERVER['SERVER_ADMIN']}<br>
                            </span>
                        </div>
                        <a href="mailto:{$eConfig['sys_error_log_email']}">Informar o erro ao desenvolvedor</a>
                        <br><br>
                    </div>
                </div>
            </div>
            </body>
        </html>
HTML;

        }else{
            $_tmp = explode("-",$this->code);
            $code = isset($_tmp[0]) ? $_tmp[0] : "";

            $html =<<<HTML
            <html>
                <head>
                    <title>Vita: Erro Capturado</title>
                    <style>
                        body{margin:0;padding:0;font-family:"Open Sans",sans-serif;}
                        h1{font-weight: 100;font-size: 50px;color:#555;margin:2% 0 0 5%;}
                        .errwrapper{background-color: #ebebeb;padding: 40px 75px;color: #444;margin-top: 31px;font-family: monaco,courier,monospace;font-size: 15px;}
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>Erro {$code}</h1>
                    </div>
                    <div class="errwrapper">
                        Arquivo: {$arquivo} <br>
                        Linha: {$this->line}
                    </div>
                </body>
            </html>
HTML;
        }

        return $html;
    }
}


/**
 *    Classe responsavel por tratar Exceptions no sistema
 *    normalmente é chamada atraves do codigo
 *    thown new SysException( "Mensagem a ser apresentada" );
 */
class SysException extends \Exception
{
    public function __construct(
        $message = null,
        $code = 0,
        $arquivo = null,
        $linha = null
    ) {
        global $eConfig;

        parent::__construct($message, $code);

        date_default_timezone_set($eConfig['default_time_zone']);
        $hoje = date($eConfig['date_format']);

        $this->file = $arquivo == null ? $this->getFile() : $arquivo;
        $this->line = $linha == null ? $this->getLine() : $linha;
        $this->message = $this->getMessage();
        $this->code = $this->getCode() . " - ".$this->getTypeError($code);
        $this->traceAsString = $this->getTraceAsString();

        $msgError = "\n ====== $hoje ======"
                  . "\n Erro no arquivo : " . $this->file
                  . "\n Linha :           " . $this->line
                  . "\n Mensagem :        " . $this->message
                  . "\n Codigo :          " . $this->code
                  . "\n Trace(str) :      " . "\n" . $this->traceAsString
                  . "\n ";

        $tmpFileDestionation = $eConfig['log_folder']
                             . date('Ymd').'_'
                             . $eConfig['sys_errorfile_destination'];

        $destination = $eConfig['sys_error_log_type'] == 3
                     ? $eConfig['sys_errorfile_destination']
                     : $eConfig['sys_error_log_email'];

        //    se vai gravar em arquivo, chega se o mesmo existe
        if ($eConfig['sys_error_log_type'] == 3) {
            if (!file_exists($tmpFileDestionation)) {
                if ($fh = fopen($tmpFileDestionation, 'w')) {
                    fclose($fh);
                    $destination = $tmpFileDestionation;
                } else {
                    $eConfig['sys_error_log_type'] = 0;
                }
            } else {
                $destination = $tmpFileDestionation;
            }
        }

        error_log($msgError, $eConfig['sys_error_log_type'], $destination);

        # checa o que o desenvolvedor setou nas configuracoes para apresentar os erros na tela

        # apresentando pagina com saida HTML
        $error = new ErrorOutput();
        $error->show_error_log = $eConfig['show_error_log'];
        $error->message = $this->message;
        $error->line = $this->line;
        $error->file = $this->file;
        $error->code = $this->code;
        $error->traceAsString = $this->traceAsString;
        print $error;

        # ja exibimos erro, nao necessario que php faca isso novamente, desligando erros do sistema
        error_reporting(0);
    }

    function getTypeError( $code__ )
    {
        switch ( $code__ ){
            case E_ERROR: return "E_ERROR"; break; // code: 1
            case E_WARNING: return "E_WARNING"; break; // code: 2
            case E_PARSE: return "E_PARSE"; break; // code: 4
            case E_NOTICE: return "E_NOTICE"; break; // code: 8
            case E_CORE_ERROR: return "E_CORE_ERROR"; break; // 1code: 6
            case E_CORE_WARNING: return "E_CORE_WARNING"; break; // 3code: 2
            case E_COMPILE_ERROR: return "E_COMPILE_ERROR"; break; // 6code: 4
            case E_COMPILE_WARNING: return "E_COMPILE_WARNING"; break; // 12code: 8
            case E_USER_ERROR: return "E_USER_ERROR"; break; // 25code: 6
            case E_USER_WARNING: return "E_USER_WARNING"; break; // 51code: 2
            case E_USER_NOTICE: return "E_USER_NOTICE"; break; // 102code: 4
            case E_ALL: return "E_ALL"; break; // 614code: 3
            case E_STRICT: return "E_STRICT"; break; // 204code: 8
            case E_RECOVERABLE_ERROR: return "E_RECOVERABLE_ERROR"; break; // 409code: 6
            default: return "UNDEFINED"; break;
        }
    }
}

/**
 * Classe responsavel por lidar e apresentar informacoes
 * acerca de erros que venham a acontecer no sistema
 */
class Error_handling
{
    public static function handler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno))
        {
            // Codigo de erro nao incluido em error_reporting
            return;
        }

        switch ($errno)
        {
            case E_USER_ERROR:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            case E_NOTICE:
            case E_WARNING:
            case E_ERROR:
                new SysException($errstr, $errno,$errfile,$errline);
                exit(1);
                break;

            default:
                echo "<b>Erro desconhecido:</b> [$errno] $errstr\n";
                break;
        }

        /* evita executar o handler interno de erros do PHP */
        return true;
    }
}


/**
* Analisa e lida com erros fatais
*/
function check_for_fatal_error()
{
    $error = error_get_last();
    if ( $error["type"] == E_ERROR ):
        Error_handling::handler(
            $error["type"],
            $error["message"],
            $error["file"],
            $error["line"]
        );
    endif;
}

register_shutdown_function( "\Framework\Vita\Core\check_for_fatal_error" );
set_error_handler( array("\Framework\Vita\Core\Error_handling","handler") );
// set_exception_handler( "log_exception" );
ini_set( "display_errors", "off" );
error_reporting( E_ALL );

