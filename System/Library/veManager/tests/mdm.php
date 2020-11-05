<?php

require_once __DIR__ . '/../src/EntityInterface.php';
require_once __DIR__ . '/../src/Entity.php';
require_once __DIR__ . '/../src/VirtualEntity.php';
require_once __DIR__ . '/../src/VeManager.php';
require_once __DIR__ . '/../src/QueryBuilderInterface.php';
require_once __DIR__ . '/../src/QueryBuilder.php';
require_once __DIR__ . '/../src/Database/Provider/ProviderInterface.php';
require_once __DIR__ . '/../src/Database/Provider/Mysql.php';


use Osians\VeManager\QueryBuilderInterface;
use Osians\VeManager\QueryBuilder;
use Osians\VeManager\Database\Provider\Mysql;
use Osians\VeManager\EntityInterface;
use Osians\VeManager\Entity;
use Osians\VeManager\VirtualEntity;
use Osians\VeManager\VeManager;


$drive = new Mysql();
$drive
    ->setHostname('localhost')
    ->setPort('3306')
    ->setUsername('wsantana')
    ->setPassword('123456')
    ->setDatabaseName('datamanager');

$pdo = $drive->connect();

$select = new QueryBuilder();

# caso de uso 0
// $select
// 	->select()
// 	->from('user')
// 	->where('active = ?', 1)
// 	->limit(1);


# caso de uso 1
// $select
// 	->select()
// 	->from(
// 		['u' => 'user'],
// 		['firstname', 'lastname', 'id_user', 'about', 'url']
// 	)
// 	->where('active = ?', 1)
// 	->limit(1);


# caso de uso 2
$select
    ->select()
    ->from(['u' => 'user'], ['*'])
    ->innerJoin(
        'user_group', 
        'user_group.id_user_group = u.id_user_group', 
        ['id_user_group', 'group_name']
    )
    ->leftJoin(
    	'user_permission', 
    	'user_permission.id_user = u.id_user', 
    	['permission_name']
    )
    ->where('u.active = ?', 1)
    ->order('u.registry_date desc');

echo PHP_EOL;
echo "----------------------------------" . PHP_EOL;
echo($select->sql()) . PHP_EOL;
echo "----------------------------------" . PHP_EOL . PHP_EOL;
// var_dump($select->getFieldOwner('maluna'));




// die();

$vem = new VeManager();
$result = $vem->setConnection($pdo)->query($select);
$entity = $result[0];

$entity->setAbout('All About Wsantana');
$entity->setUrl('http://sooho.com.br');
$entity->setGroupName("Administrator");
// $entity->setPermissionName('Default');

// print_r($entity);
// print_r($select->getFieldMap());


$vem->save($entity);
// $result[0]->save();

die();





$select = new QueryBuilder();

$select->select(['u.*', 'ug.group_name'])
       ->from(['u' => 'user'])
       ->innerJoin(['ug' => 'user_group'], 'ug.id_user_group = u.id_user_group', ['id_user_group'])
       ->leftJoin(['user_permission'], 'user_permission.id_user = u.id_user', ['permission_name'])
       ->where('u.active = ?', 1)
       ->order('registry_date desc');


$vem = new DtManager();
$result = $vem->setConnection($pdo)->query($select);

print_r($result[0]);
print_r($result[0]->getEmail());
print_r($select->getFieldMap());


