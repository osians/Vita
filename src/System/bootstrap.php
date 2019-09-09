<?php

namespace Framework\Vita;

/**
 * Bootstrap file
 *
 * @package Vita
 */

# implementa objeto que armazena as variavies do sistema
require_once 'Core/Config/Config.php';

# gerencia posts dos formularios
require_once 'Core/Post.php';

# metodos uteis do sistema
require_once 'Core/Utils.php';

# filtros e regras de validacao
require_once 'Core/Validate/Validate.php';

# gerencia sessoes
require_once 'Core/Session.php';

# registra logs do sistema
require_once 'Core/Log/Log.php';

# permite controlar analisar o tempo de processos
require_once 'Core/sys_benchmark.class.php';

# interface PDO para bancos de dados
require_once 'Core/Database/Factory.php';

# interface PDO para bancos de dados
require_once 'Core/Database/Provider/Mysql.php';

# facilita buscas manipulaçao de tabelas no banco de dados
require_once 'Core/sys_table.class.php';

# gerencia uploads
require_once 'Core/Upload.php';

# gerenciador de emails PHPMailer
require_once 'Libraries/phpmailer/PHPMailerAutoload.php';
