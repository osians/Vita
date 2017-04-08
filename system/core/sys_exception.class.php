<?php

header('Content-Type: text/html; charset=utf-8');

#
# tenta pegar informacoes de configuracoes externas a
# este arquivp, mas...
global $_config;

#
# Exception nao pode depender de um informacoes externas
# logo, deve haver uma garantia de que essa classe
# funcione de forma independente de qualquer outra.
# Sendo assim, se chegaru ma chamada aqui, a mesma
# deve garantir o processamento da mesma sem erros.
$_sx_cfg = array();

$_sx_cfg['default_time_zone']         = isset($_config['default_time_zone'])         ?  $_config['default_time_zone'] : 'Brazil/East';
$_sx_cfg['date_format']               = isset($_config['date_format'])               ?  $_config['date_format'] : 'Y-m-d H:i:s';
$_sx_cfg['sys_error_log_type']        = isset($_config['sys_error_log_type'])        ?  $_config['sys_error_log_type'] : 3 ;
$_sx_cfg['sys_errorfile_destination'] = isset($_config['sys_errorfile_destination']) ?  $_config['sys_errorfile_destination'] : 'sys_erros.dat';
$_sx_cfg['log_folder']                = isset($_config['log_folder'])                ?  $_config['log_folder'] : 'system/log/';
$_sx_cfg['sys_error_log_email']       = isset($_config['sys_error_log_email'])       ?  $_config['sys_error_log_email'] : 'seu.email.dev@seudominio.com.br';
$_sx_cfg['show_error_log']            = isset($_config['show_error_log'])            ?  $_config['show_error_log'] : TRUE;
$_sx_cfg['show_extra_log']            = isset($_config['show_extra_log'])            ?  $_config['show_extra_log'] : TRUE;

#
# Extra Log
# caso uma informacao seja setada 
# neste array, sera exibido ao final do log
$_sx_cfg['elog'] = array();

/**
 * Funcao para uso Debug
 * Basicamente, insere informacoes 
 * a serem exibidas na tela de erro, 
 * alem das rotineiras.
 */
function sys_exception_debug( $__info = null ){
    global $_sx_cfg;
    $_sx_cfg['elog'][] = $__info;
}

/**
 * Classe responsavel por apresentar uma Pagina HTML Amigavel
 * contendo um dado erro encontrado
 */
class Error_Output
{
    public function __set($name,$value)
    {
        $this->$name = htmlspecialchars($value);
    }

    public function __toString()
    {
        global $_sx_cfg;

        if (ob_get_contents()) ob_end_clean();
        date_default_timezone_set( $_sx_cfg['default_time_zone'] );
        $data_formatada = date( $_sx_cfg['date_format'], $_SERVER['REQUEST_TIME'] );
        $trace = str_replace("#", "<br>#", $this->traceAsString);
        $__arquivo__ = basename( $this->file );
        $__full_request_uri__ = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        $this->trecho_com_erro = "";

        $dbp = (isset(Vita::getInstance()->config->dbpass)) ? Vita::getInstance()->config->dbpass : false;
        $dbu = (isset(Vita::getInstance()->config->dbuser)) ? Vita::getInstance()->config->dbuser : false;
        if( $dbp ) {
            $this->message = str_replace( $dbp, '*************', $this->message );
            $this->message = str_replace( $dbu, '*************', $this->message );
        }
        
        // obtendo codigo do arquivo que deu erro
        $handle = fopen($this->file, "r");

        if ($handle):
            $__linha_atual__ = 1;
            $__inicio_trecho__= $this->line - 3;
            $__final_trecho__ = $this->line + 2;
            while (($line = fgets($handle)) !== false){
                // obtem apenas 3 linhas de código
                if($__linha_atual__ >= $__inicio_trecho__){
                    if($__linha_atual__ == $this->line) $this->trecho_com_erro .= "<b>";
                    $this->trecho_com_erro .= "{$__linha_atual__}: {$line}";
                    if($__linha_atual__ == $this->line)
                        $this->trecho_com_erro .= "</b>";
                    $this->trecho_com_erro .= "<br>";

                    if($__linha_atual__ == $__final_trecho__) break;
                }
                $__linha_atual__++;
            }
            fclose($handle);
        else:
            // error opening the file.
        endif;

        // checa se a dump extra dentro de elog.
        $__elog = (!empty($_sx_cfg['elog'])) ? implode( "<br>", $_sx_cfg['elog'] ) : null;
        $__elog = (null == $__elog) ? "" : '<span class="elog"><b>Extra Log:</b> <br>'. $__elog . '</span><br><br>';

        // verificando se devemos apresentar o extra log 
        $__show_extra_log = ($_sx_cfg['show_extra_log'] == FALSE) ? "display:none;" : "";

        // verificando se deve apresentar uma versao mais light de erros 
        if($this->show_error_log)
        {

        $__html__ =<<<HTML
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
                    a{color:#C6E863;display:block;}
                    div.errwrapper{
                        font-size:16px;
                        font-family:monospace;
                    }
                    div.errmais{
                        color:#9AA2A9;
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
                        {$__show_extra_log}
                    }
                    span.msg_errro{color:#DD982E;background-color:#202024;display:block;padding: 10px 25px;}
                </style>
            </head>
            <body>
            <div class="container">
                <br>
                <h1>Erro em: {$__arquivo__}</h1>

                <div class="errwrapper">
                    <div style="">
                        <span class="msg_errro">
                            {$this->message}
                        </span>
                        <span class='trecho_code_err'>
                            {$this->trecho_com_erro}
                        </span>
                        <br>

                        {$__elog}

                        <div class="errmais">

                            <b>Mensagem:</b>   {$this->message} <br>
                            <b>Arquivo</b>:    {$this->file} (Linha: {$this->line}) <br>
                            <b>Código</b>:     {$this->code} <br>
                            <b>Trace(str):</b> {$trace} <br>
                            <br><br>
                            
                            <span class='show_extra_log'>
                            <b>Dados adicionais</b> <br>
                            REQUEST_URI: {$__full_request_uri__}<br>
                            HTTP_ACCEPT:  {$_SERVER['HTTP_ACCEPT']} <br>
                            USER_AGENT :  {$_SERVER['HTTP_USER_AGENT']} <br>
                            REMOTE_ADDR:  {$_SERVER['REMOTE_ADDR']} <br>
                            REQUEST_TIME: {$data_formatada}<br>
                            SERVER_ADMIN: {$_SERVER['SERVER_ADMIN']}<br>
                            </span>
                        </div>
                        <br>
                        <a href="mailto:{$_sx_cfg['sys_error_log_email']}">Informar o erro ao desenvolvedor</a>
                    </div>
                </div>
            </div>
            </body>
        </html>
HTML;
        
        }else{
            $_tmp = explode("-",$this->code);
            $code = isset($_tmp[0]) ? $_tmp[0] : "";

            $__html__ =<<<HTML
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
                        Arquivo: {$__arquivo__} <br> 
                        Linha: {$this->line}
                    </div>
                </body>
            </html>       
HTML;
        }

        return $__html__;
    }
}


/**
 * Classe responsavel por tratar Exceptions no sistema
 * normalmente é chamada atraves do codigo
 * thown new SYS_Exception( "Mensagem a ser apresentada" );
 */
class SYS_Exception extends Exception
{
    function __construct($message = null, $code = 0, $arquivo = null, $linha = null )
    {

        global $_sx_cfg;

        parent::__construct( $message, $code );

        date_default_timezone_set( $_sx_cfg['default_time_zone'] );
        $hoje = date( $_sx_cfg['date_format'] );

        $this->file = $arquivo == null ? $this->getFile() : $arquivo;
        $this->line = $linha == null ? $this->getLine() : $linha;
        $this->message = $this->getMessage();
        $this->code = $this->getCode() . " - ".$this->getTypeError($code);
        $this->traceAsString = $this->getTraceAsString();

        $msg_error =
            "\n====== $hoje ======" .
            "\nErro no arquivo : " . $this->file.
            "\nLinha :           " . $this->line .
            "\nMensagem :        " . $this->message .
            "\nCodigo :          " . $this->code .
            "\nTrace(str) :      " . "\n" . $this->traceAsString . "\n";

        $__tmp_file_destionation =  VTPATH . $_sx_cfg['log_folder'] . date('Ymd').'_'. $_sx_cfg['sys_errorfile_destination'] ;
        $__destination = $_sx_cfg['sys_error_log_type'] == 3 ? $_sx_cfg['sys_errorfile_destination'] : $_sx_cfg['sys_error_log_email'];
        
        # se vai gravar em arquivo, chega se o mesmo existe
        if($_sx_cfg['sys_error_log_type'] == 3)
        {
            // se o arquivo nao existe...
            if( !file_exists( $__tmp_file_destionation ) )
            {
                // tenta criar o arquivo, 
                if($fh = fopen($__tmp_file_destionation,'w')){
                    fclose($fh);
                    $__destination = $__tmp_file_destionation;
                }else{
                    $_sx_cfg['sys_error_log_type'] = 0 ; # grava no log do sistema
                }
            }
            else /*se arquivo existe, entao grava nele*/
            {
                $__destination = $__tmp_file_destionation;
            }
        }

        error_log( $msg_error, $_sx_cfg['sys_error_log_type'], $__destination );

        # checa o que o desenvolvedor setou nas configuracoes para apresentar os erros na tela

        # apresentando pagina com saida HTML
        $error = new Error_Output();
        $error->show_error_log = $_sx_cfg['show_error_log'];
        $error->message = $this->message;
        $error->line = $this->line;
        $error->file = $this->file;
        $error->code = $this->code;
        $error->traceAsString = $this->traceAsString;
        print $error;

        # ja exibimos erro, nao necessario que php faca isso novamente, desligando erros do sistema
        error_reporting(0);
    }

    function getTypeError( $__code__ )
    {
        switch ( $__code__ ){
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
                new SYS_Exception($errstr, $errno,$errfile,$errline);
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

register_shutdown_function( "check_for_fatal_error" );
set_error_handler( array("Error_handling","handler") );
// set_exception_handler( "log_exception" );
ini_set( "display_errors", "off" );
error_reporting( E_ALL );

