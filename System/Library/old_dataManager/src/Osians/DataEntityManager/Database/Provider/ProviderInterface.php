<?php

namespace Osians\DataEntityManager\Database\Provider;

interface ProviderInterface
{
	/**
	 *    Realiza uma conexao com o Banco de dados
	 *    e retorna um driver PDO
	 *    @return \PDO
	 */
    public function conectar();
}
