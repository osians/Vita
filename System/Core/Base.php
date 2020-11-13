<?php


namespace Vita\Core;

use Vita\Vita;

/**
 * Class Base
 *
 * Permite a criacao de Classes que fornecem apenas dados para
 * objetos como controllers.
 *
 * @package Vita\Core
 */
class Base
{
    /**
     * Unique Instance
     *
     * @var Base
     */
    protected static $_instance = null;

    /**
     * No Construct allowed
     */
    public function __construct()
    {
    }

    /**
     * Retorna a instância singleton do objeto
     */
    public static function getInstance()
    {
        $class = self::_getCalledClass();
        if (self::$_instance === null || !is_a(self::$_instance, $class)) {
            self::$_instance = new $class();
        }
        return self::$_instance;
    }

    /**
     * Retorna o nome da classe
     * @return string
     */
    private static function _getCalledClass()
    {
        if (function_exists('get_called_class')) {
            return get_called_class();
        }

        $bt = debug_backtrace();
        return $bt[2]['class'] . 'Base';
    }

    /**
     * Sql Query
     * @param string $sql
     * @return array - return Array of Objects
     */
    protected function query($sql)
    {
        $em = Vita::getInstance()->getEntityManager();
        $stm = $em->getConnection()->prepare($sql);
        $stm->execute();

        return ($stm->rowCount() > 0) ? $stm->fetchAll(\PDO::FETCH_OBJ) : array();
    }
}
