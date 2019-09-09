<?php

# Se a informação abaixo for definida como true
# O Sistema carregara apenas até o ponto em
# que o objeto $vita se torna disponível.
# Isto é util para Programadores que desejam apenas as
# Funções básicas fornecidas pelo sistema, ou desejam criar
# um modelo proprio, diferente do MVC que
# é implementado por este sistema.
defined('VITAONLY') or define('VITAONLY', false);


# WEBSITE
# --------------------------------------------------------
# Enderecos do sistema
$config['site_name'] = 'App Name';
$config['url']       = 'http://vita.example.com/';


# DATABASE
# --------------------------------------------------------
# credenciais de acesso ao banco de dados MySQL
# caso nao queria iniciar o database MySQL por padrao
# sete o item abaixo para false.
$config['load_mysql'] = false;

# Porta ede coenxao ao SGBD, por padrao o MySQL trabalha na porta 3306
$config['dbport'] = '3306';

# IP ou url para o Banco de dados
$config['dbhost'] = 'localhost';

# Nome do banco de dados que sera utilizado
$config['dbname'] = 'vita';

# Nome de username com acesso ao banco de dados
$config['dbuser'] = 'root';

# password de acesso
$config['dbpass'] = '';


# DATETIME
# --------------------------------------------------------
# timezone a ser usado para registros no sistema
$config['default_time_zone'] = 'Brazil/East';

# default date and time format
$config['date_format'] = 'Y-m-d H:i:s';


# LOG
# --------------------------------------------------------
# Se true as chamadas a classe log devem ser executadas
$config['enable_log'] = false;

# pasta para registro de logs, a partir da raiz
# /vita/system/log/
$config['log_folder'] = 'System/Log/';


# SESSION
# --------------------------------------------------------
# definir um tempo para expiracao da sessao apos tempo 
# inativo (0 = nunca expira)
$config['session_expire_time'] = 3600; // 1 horas


# UPLOAD
# --------------------------------------------------------
# caminho ondem devem ser salvos os uploadas
$config['upload_folder'] = 'Files/Upload/';
$config['max_img_width'] = 1900;
$config['max_img_height'] = 900;
$config['max_file_size'] = 2097152; // 2mb


# SQLITE
# --------------------------------------------------------
# se true instancia tambem o SQLite como banco de dados
$config['load_sqlite'] = false;

# credenciais SQLite
$config['sqlite_dbname'] = 'system.sqlite3';

# localizacao do banco de dados sqllite e suas tabelas...
$config['sqlite_folder'] = "Databases";


# DEBUG
# --------------------------------------------------------
# @var bool - informa se deve ou nao apresentar erros
# ocorridos na tela para o usuario
# Se false, Vita sera executado no modo de Producao
$config['vita_dev_mode']  = true;

#    Indica se o erro apresentado ser no estilo Vita ou PHP Nativo
#    true - Vita, false - PHP Nativo
$config['vita_error_style']  = true;

# Apresenta mais detalhes sobre um erro ocorrido no sistema
$config['show_error_log'] = true;
$config['show_extra_log'] = false;

# Tipo de log que deve ser realizado, em arquivo ou envio por email
# @var int - se 3 gravar em arquivo, caso 1 enviar por email ao administrador
$config['sys_error_log_type'] = 3;

# em caso de erro, e ERROR_LOG_TYPE = 1 enviar mensagem para ....
$config['sys_error_log_email'] = 'sans.pds@gmail.com';

# arquivo para onde se destinal os logs de erro...
$config['sys_errorfile_destination'] = 'sys_erros.dat';


# TWIG
# --------------------------------------------------------
# @var bool - informa se deve iniciar sistema de tradução do TWIG ou não
$config['twig_localization_enabled'] = false;

# idioma padrao do sistema
$config['twig_localization_locale'] = 'pt_BR';

# localizacao do arquivo com as traducoes
$config['twig_localization_locale_path'] = 'Config/Locale/';

# @var bool - se true, twig ira criar cache das paginas processadas.
$config['twig_cache_enable'] = false;

# @var bool - se true, twig ira adicionar a extensao de debug
$config['twig_debug_enable'] = false;
