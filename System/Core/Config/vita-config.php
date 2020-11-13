<?php

/**
* Nota: Esse arquivo pode ser alterado automaticamente
* pelo sistema, logo, espaçamentos separando as informações,
* podem se perder. Comentários serão mantidos, mesmos aqueles
* escritos apos o sinal de ";"
* 
*/

# -------------------------------------------------------
# WEBSITE
# -------------------------------------------------------
$configs['site_name'] = 'App Name';
$configs['url'] = 'http://localhost/vitaui/';


# -------------------------------------------------------
# DATABASE
# -------------------------------------------------------
# Porta ede conexão ao SGBD, por padrão o MySQL trabalha na porta 3306
$configs['dbport'] = '3306';

# IP ou url para o Banco de dados
$configs['dbhost'] = 'localhost';

# Nome do banco de dados que sera utilizado
$configs['dbname'] = 'vita';

# Nome de username com acesso ao banco de dados
$configs['dbuser'] = 'user';

# password de acesso
$configs['dbpass'] = 'passe';

# loads VEM - Virtual Entity Manager
$configs['load_data_manager'] = TRUE;

# -------------------------------------------------------
# DATETIME
# --------------------------------------------------------
# timezone a ser usado para registros no sistema
$configs['default_time_zone'] = 'Brazil/East';

# default date and time format
$configs['date_format'] = 'Y-m-d H:i:s';


# -------------------------------------------------------
# LOG
# --------------------------------------------------------
# Se true as chamadas a classe log devem ser executadas
$configs['enable_log'] = false;

# pasta para registro de logs, a partir da raiz
# /vita/system/log/
$configs['log_folder'] = 'System/Core/Log/Logs/';


# -------------------------------------------------------
# SESSION
# --------------------------------------------------------
# definir um tempo para expiração da sessão apos tempo
# inativo (0 = nunca expira)
$configs['session_expire_time'] = 3600; // 1 horas


# -------------------------------------------------------
# UPLOAD
# --------------------------------------------------------
# caminho ondem devem ser salvos os uploads
$configs['upload_folder'] = 'Files/Upload/';
$configs['max_img_width'] = 1900;
$configs['max_img_height'] = 900;
$configs['max_file_size'] = 2097152; // 2mb


# -------------------------------------------------------
# SQLITE
# --------------------------------------------------------
# se true instancia também o SQLite como banco de dados
$configs['load_sqlite'] = false;

# credenciais SQLite
$configs['sqlite_dbname'] = 'system.sqlite3';

# localização do banco de dados sql lite e suas tabelas...
$configs['sqlite_folder'] = 'Databases';


/**
 * -------------------------------------------------------
 * DEBUG
 * --------------------------------------------------------
 * @var bool - informa se deve ou nao apresentar erros
 * ocorridos na tela para o usuário
 * Se false, Vita sera executado no modo de produção
 * @deprecated
 */
$configs['vita_dev_mode'] = true;

# Indica se o erro apresentado ser no estilo Vita ou PHP Nativo
# true - Vita, false - PHP Nativo
$configs['vita_error_style'] = true;

# Apresenta mais detalhes sobre um erro ocorrido no sistema
$configs['show_error_log'] = true;
$configs['show_extra_log'] = false;

# Tipo de log que deve ser realizado, em arquivo ou envio por email
# @var int - se 3 gravar em arquivo, caso 1 enviar por email ao administrador
$configs['sys_error_log_type'] = 3;

# em caso de erro, e ERROR_LOG_TYPE = 1 enviar mensagem para ....
$configs['sys_error_log_email'] = 'sans.pds@gmail.com';

# arquivo para onde se destina os logs de erro...
$configs['sys_errorfile_destination'] = 'sys_erros.dat';


# -------------------------------------------------------
# TWIG
# --------------------------------------------------------
# @var bool - informa se deve iniciar sistema de tradução do TWIG ou não
$configs['twig_localization_enabled'] = false;

# idioma padrão do sistema
$configs['twig_localization_locale'] = 'pt_BR';

# localização do arquivo com as traduções
$configs['twig_localization_locale_path'] = 'Config/Locale/';

# @var bool - se true, twig ira criar cache das paginas processadas.
$configs['twig_cache_enable'] = false;

# @var bool - se true, twig ira adicionar a extensão de debug
$configs['twig_debug_enable'] = false;
