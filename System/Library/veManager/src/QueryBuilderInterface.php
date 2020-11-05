<?php

namespace Osians\VeManager;

interface QueryBuilderInterface
{
    /**
     * This needs to return the name of the Main Table in 
     * which the query is taking place.
     *
     * @return String 
     */
    public function getTableName();

    /**
     * Return raw Sql Query
     * 
     * @return String
     */
    public function sql();
}
