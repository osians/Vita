<?php

namespace Osians\VeManager\Database\Provider;

interface ProviderInterface
{
    /**
     * Creates a database connection and returns a PDO driver
     *
     * @return \PDO
     */
    public function connect();
}
