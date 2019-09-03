<?php

namespace Vita\Core\Database;

if (!defined('ALLOWED')) {
    exit('Acesso direto ao arquivo nao permitido.');
}

class SystemCoreDatabaseFactory
{
    /**
    * @param string  - Tipo de banco de dados a ser criado conexao
    * @return object - Objeto de conexao ao DB escolhido
    */
    public static function create($db = 'MySQL')
    {
        $_args_ = func_get_args();
        $_extra_args = isset($_args_[1]) ? $_args_[1] : null;

        $db = strtolower($db);

        switch ($db) {
            case 'mysql':

                $db = new SystemCoreDatabaseProviderMysql();

                if( isset($_extra_args['host'])&&
                    isset($_extra_args['port'])&&
                    isset($_extra_args['user'])&&
                    isset($_extra_args['pass'])&&
                    isset($_extra_args['dbname']) )
                {
                    $db->setHost( $_extra_args['host'] );
                    $db->setDbport( $_extra_args['port'] );
                    $db->setUser( $_extra_args['user'] );
                    $db->setPass( $_extra_args['pass'] );
                    $db->setDbname( $_extra_args['dbname'] );
                }

                $conn = $db->conectar();
                return new SystemCoreDatabaseDb($conn);
            break;

            case 'sqllite':
                $db = new SQLliteProvider();

                if(isset($_extra_args['dbpath'])&&isset($_extra_args['dbname']))
                {
                    $db->setDBPath( $_extra_args['dbpath'] );
                    $db->setDBName( $_extra_args['dbname'] );
                }

                $conn = $db->conectar();
                return new SystemCoreDatabaseDb($conn);
            break;

            default:
                return new \stdClass(); # objeto null
            break;
        }
    }
}