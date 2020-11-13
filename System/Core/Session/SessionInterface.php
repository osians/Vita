<?php

namespace Vita\Core\Session;

/**
 * Session Interface
 *
 * @package Vita
 * @subpackage Core
 * @category Sessions
 * @author Wanderlei Santana <sans.pds@gmail.com>
 * @link https://github.com/osians
 **/
interface SessionInterface
{
    /**
     *    Set a value in the session
     *
     *    @param String $name
     *    @param Mixed $value
     *
     *    @return Session
     **/
    public function set($name, $value);

    /**
     *   Get value From Session by name
     *
     *   @param String $name
     *
     *   @return Mixed | null
     */
    public function get($name);

    /**
     *   Destroy All model from Session
     *
     *   @return void
     **/
    public function destroy();
}
