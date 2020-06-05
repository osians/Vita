<?php

namespace Osians\DataEntityManager;

interface QueryBuilderInterface
{
	/**
	 *    Deve retornar o nome da Tabela Principal
	 *    em que a consulta esta ocorrendo.
	 *
	 *    @return String 
	 */
	public function getTableName();

	/**
	 *    Return raw Sql Query
	 * 
	 *    @return String
	 */
	public function sql();
}
