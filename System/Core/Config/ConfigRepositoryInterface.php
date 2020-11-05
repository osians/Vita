<?php

namespace Vita\Core\Config;

interface ConfigRepositoryInterface
{
    /**
     * Obtêm todas as configurações
     *
     * @return array
     */
    public function getAll();

    /**
     * Salva dados de configuração alterados
     * 
     * @param array $config
     *
     * @return ConfigRepositoryInterface
     */
    public function save($config);
}
