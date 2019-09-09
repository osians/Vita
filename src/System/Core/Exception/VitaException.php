<?php

namespace Vita\System\Core\Exception;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Config.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'ErrorOutput.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'ErrorHandler.php';

/**
 *    Classe responsavel por tratar Exceptions no sistema
 *    normalmente é chamada atraves do codigo
 *    thown new SysException( "Mensagem a ser apresentada" );
 */
class VitaException extends \Exception
{
    public function __construct($message = null, $code = 0, $arquivo = null, $linha = null)
    {
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

    function getTypeError($errorCode)
    {
        switch ($errorCode)
        {
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
*    Lida com erros Fatais
*/
function checkForFatalError()
{
    $error = error_get_last();
    if ($error["type"] == E_ERROR) {
        ErrorHandler::handler(
            $error["type"],
            $error["message"],
            $error["file"],
            $error["line"]
        );
    }
}

if ($config['vita_error_style'] == true)
{
    register_shutdown_function('\Vita\System\Core\Exception\checkForFatalError');
    set_error_handler(array('\Vita\System\Core\Exception\ErrorHandler', 'handler'));
    // set_exception_handler( "log_exception" );
    // ini_set("display_errors", "off");
    error_reporting(E_ALL);
}
