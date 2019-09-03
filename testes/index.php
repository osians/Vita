<?php

$ds = DIRECTORY_SEPARATOR;
require_once __DIR__ . "{$ds}..{$ds}vitaloader.php";

# ------------------------------------------
# daqui em diante, o sistema todo e' gerido
# atraves de objetos, e pode ser acessado por:
# vita(), vita() ou System::getInstance().
#
# logo, para obter uma configuração setada no
# arquivo config.php por exemplo, pode-se usar:
//echo $vita->config->dbname;
# vita()->config->dbname; (ou vita()->config->get( 'dbname' );)
# vita()->config->dbname;
# System::getInstance()->config->dbname;

#    Testtando Objeto de Log
#    1. Teste de Escrita
$vita->log->write('Hallo Welt!');


