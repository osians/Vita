<?php

#
# GERAL
#
# --------------------------------------------------------
# essa definicao impede que arquivos do
# sistema sejam acessados direto.
# sem uma previa inicializacao.
define( 'ALLOWED', TRUE );

#
# Se a informação abaixo for definida como TRUE
# O Sistema carregara apenas até o ponto em
# que o objeto $vita se torna disponível.
# Isto é util para Programadores que desejam apenas as
# Funções básicas fornecidas pelo sistema, ou desejam criar
# um modelo proprio, diferente do MVC que
# é implementado por este sistema.
defined('VITAONLY') or define( 'VITAONLY', FALSE );

#
# WEBSITE
#
# --------------------------------------------------------
# Enderecos do sistema
$_config['site_name'] = 'App Name' ;
$_config['url']       = 'http://vita.example.com/' ;

#
# DATABASE
#
# --------------------------------------------------------
# credenciais de acesso ao banco de dados MySQL
# caso nao queria iniciar o database MySQL por padrao
# sete o item abaixo para false.
#
$_config['load_mysql'] = TRUE;

# Porta ede coenxao ao SGBD, por padrao o MySQL trabalha na porta 3306
$_config['dbport'] = '3306';

# IP ou url para o Banco de dados
$_config['dbhost'] = 'localhost';

# Nome do banco de dados que sera utilizado
$_config['dbname'] = 'vita';

# Nome de username com acesso ao banco de dados
$_config['dbuser'] = 'root';

# password de acesso
$_config['dbpass'] = '';

#
# DATETIME
#
# --------------------------------------------------------
# timezone a ser usado para registros no sistema
$_config['default_time_zone'] = 'Brazil/East' ;
# default date and time format
$_config['date_format'] = 'Y-m-d H:i:s' ;

#
# LOG
#
# --------------------------------------------------------
# Se true as chamadas a classe log devem ser executadas
$_config['enable_log'] = FALSE ;

#
# SESSION
#
# --------------------------------------------------------
# definir um tempo para expiracao da sessao apos tempo inativo (0 = nunca expira)
$_config['session_expire_time'] = 3600 ; // 1 horas

#
# UPLOAD
#
# --------------------------------------------------------
# caminho ondem devem ser salvos os uploadas
$_config['upload_folder'] = 'files/upload/';
$_config['max_img_width'] = 1900;
$_config['max_img_height'] = 900;
$_config['max_file_size'] = 2097152; // 2mb

#
# SQLITE
#
# --------------------------------------------------------
# se TRUE instancia tambem o SQLite como banco de dados
$_config['load_sqlite'] = false;

# credenciais SQLite
$_config['sqlite_dbname'] = 'system.sqlite3' ;

# localizacao do banco de dados sqllite e suas tabelas...
$_config['sqlite_folder'] = "databases" ;

#
# DEBUG
# 
# --------------------------------------------------------
# @var bool - informa se deve ou nao apresentar erros ocorridos na tela para o usuario
$_config['vita_dev_mode']  = TRUE ; /* Se false, Vita sera executado no modo de Producao */
$_config['show_error_log'] = TRUE ; /* Apresenta mais detalhes sobre um erro ocorrido no sistema */
$_config['show_extra_log'] = FALSE ;

# Tipo de log que deve ser realizado, em arquivo ou envio por email
# @var int - se 3 gravar em arquivo, caso 1 enviar por email ao administrador
$_config['sys_error_log_type'] = 3 ;

# pasta para registro de logs, a partir da raiz
# /vita/system/log/
$_config['log_folder'] = 'system/log/';

# em caso de erro, e ERROR_LOG_TYPE = 1 enviar mensagem para ....
$_config['sys_error_log_email'] = 'sans.pds@gmail.com' ;

# arquivo para onde se destinal os logs de erro...
$_config['sys_errorfile_destination'] = 'sys_erros.dat' ;

#
# TWIG
#
# --------------------------------------------------------
# @var bool - informa se deve iniciar sistema de tradução do TWIG ou não
$_config['twig_localization_enabled'] = FALSE;

# idioma padrao do sistema
$_config['twig_localization_locale'] = 'pt_BR';

# localizacao do arquivo com as traducoes
$_config['twig_localization_locale_path'] = 'config/locale/';

# @var bool - se true, twig ira criar cache das paginas processadas.
$_config['twig_cache_enable'] = false;

# @var bool - se true, twig ira adicionar a extensao de debug
$_config['twig_debug_enable'] = false;
