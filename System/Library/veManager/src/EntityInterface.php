<?php

namespace Osians\VeManager;

use stdClass;

interface EntityInterface
{
    /**
     * Set Entity ID
     *
     * @param int $id
     *
     * @return  Entity
     */
    public function setId($id);

    /**
     * Returns Entity ID
     *
     * @return int
     */
    public function getId();
    
    /**
     * Initializes the current Entity with data from a StdClass Object.
     * Usually sent by EntityManager
     *
     * @param Stdclass $object
     *
     * @return void
     */
    public function init(\StdClass $object);

    /**
     * Returns the Name of the table to which the Entity refers.
     * If it's not set in the child entity, the class name will be 
     * used as the table name by default.
     *
     * @return String
     */
    public function getTableName();

    /**
     * Returns the name of the column that holds the Entity's Primary Key.
     * By default the name is made up of "id_ + class_name" in lower case.
     *
     * @return String
     */
    public function getPrimaryKeyName();
}
