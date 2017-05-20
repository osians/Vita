<?php

namespace Framework\Vita;

/**
 * Bootstrap file
 *
 * @package Vita
 */

# cria padronizacao dos objetos que exibem informacao no template html
require_once 'core/sys_vitalib.class.php';

# implementa objeto que armazena as variavies do sistema
require_once 'core/sys_config.class.php';

# gerencia posts dos formularios
require_once 'core/sys_post.class.php';

# metodos uteis do sistema
require_once 'core/sys_utils.class.php';

# filtros e regras de validacao
require_once 'core/sys_validate.class.php';

# gerencia sessoes
require_once 'core/sys_session.class.php';

# registra logs do sistema
require_once 'core/sys_log.class.php';

# permite controlar analisar o tempo de processos
require_once 'core/sys_benchmark.class.php';

# interface PDO para bancos de dados
require_once 'core/sys_db.class.php';

# facilita buscas manipulaçao de tabelas no banco de dados
require_once 'core/sys_table.class.php';

# gerencia uploads
require_once 'core/sys_upload.class.php';

# gerenciador de emails PHPMailer
require_once 'libraries/phpmailer/PHPMailerAutoload.php';