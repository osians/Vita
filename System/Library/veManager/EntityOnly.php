<?php

require_once __DIR__ . '/../src/EntityInterface.php';
require_once __DIR__ . '/../src/Entity.php';

class User extends Osians\VeManager\Entity
{
    protected $_name = null;
    protected $_age = null;
    protected $_email = null;
}



$entity = new User();
$entity->setId(4);
$entity->setName('John Doe');
$entity->setAge(54);
$entity->setEmail('john.doe@acmeinc.com');
var_dump($entity->getName());
