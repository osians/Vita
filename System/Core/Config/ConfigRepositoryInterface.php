<?php

namespace Vita\Core\Config;

interface ConfigRepositoryInterface
{
    /**
     * Obtem todas as configuracoes
     *
     * @return array
     */
    public function getAll();

    /**
     * Salva dados de configuração alterados
     * 
     * @param Array $config
     *
     * @return ConfigRepositoryInterface
     */
    public function save($config);
}
