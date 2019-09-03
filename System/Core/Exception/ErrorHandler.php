<?php

namespace Vita\System\Core\Exception;

/**
 * Classe responsavel por lidar e apresentar informacoes
 * acerca de erros que venham a acontecer no sistema
 */
class ErrorHandler
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