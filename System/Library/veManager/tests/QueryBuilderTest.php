<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use Osians\VeManager\VeManager;
use Osians\VeManager\QueryBuilder;

class QueryBuilderTest extends TestCase
{
    protected $_vem = null;
    
    public function setUp()
    {
        # Conexao...
        $provider = new \Osians\VeManager\Database\Provider\Mysql();
        $provider
            ->setHostname('localhost')
            ->setPort('3306')
            ->setUsername('wsantana')
            ->setPassword('123456')
            ->setDatabaseName('datamanager');
        
        $connection = $provider->connect();
        $this->_vem = new VeManager($connection);
    }
    
    public function testEntityReturn()
    {
        $query = new QueryBuilder();
        $query->select()->from('user')->where('id_user = ?', 4);
        $result = $this->_vem->query($query);
        $this->assertInstanceOf('\Osians\VeManager\Entity', $result[0]);
    }
    
    public function testSelectQuery()
    {
        $expectedResult = "SELECT `user`.`*` FROM `user` WHERE (user.active = 1) LIMIT 0, 1";

        $qb = new QueryBuilder();
        $qb->select()
           ->from('user')
           ->where('user.active = ?', 1)
           ->limit(1);

        $result = md5($qb->sql()) == md5($expectedResult);
        $this->assertTrue($result);
    }
    
    public function testSelectInnerQuery()
    {
        $expected = "SELECT `user`.`*`, `user_address`.`id_user_address` FROM `user` INNER JOIN `user_address` ON user_address.id_user = user.id_user WHERE (user.active = 1) LIMIT 0, 1";

        $qb = new QueryBuilder();
        $qb->select()
           ->from('user')
           ->innerJoin('user_address', 'user_address.id_user = user.id_user')
           ->where('user.active = ?', 1)
           ->limit(1);

        $result = md5($qb->sql()) == md5($expected);
        $this->assertTrue($result);
    }

    /**
     * Create a query and return some values
     *
     * @return void
     */
    public function testSelectInnerQueryGetSomeValues()
    {
        $expected = "SELECT `user`.`name`, `user_address`.`id_user_address`, `user_address`.`id_user`, `user_address`.`id_address` FROM `user` INNER JOIN `user_address` ON user_address.id_user = user.id_user WHERE (user.active = 1) LIMIT 0, 1";

        $qb = new QueryBuilder();
        $qb->select()
           ->from('user', ['name'])
           ->innerJoin('user_address', 'user_address.id_user = user.id_user', ['id_user_address', 'id_user', 'id_address'])
           ->where('user.active = ?', 1)
           ->limit(1);

        $result = md5($qb->sql()) == md5($expected);
        $this->assertTrue($result);
    }

    public function testSelectComplexyQuery()
    {
        $expected = "SELECT `usuario`.`*`, `ua`.`id_user_address`, `endereco`.`address`, `endereco`.`number`, `endereco`.`postal_code`, `endereco`.`active`, `endereco`.`id_address`, `address_type`.`type`, `address_type`.`id_address_type` FROM `user` AS `usuario` INNER JOIN `user_address` AS `ua` ON ua.id_user = usuario.id_user INNER JOIN `address` AS `endereco` ON endereco.id_address = ua.id_address INNER JOIN `address_type` ON address_type.id_address_type = endereco.id_address_type WHERE (endereco.active = 1) AND (usuario.active = 1) AND ((usuario.id_user = 2 OR endereco.id_address = 4)) ORDER BY `usuario`.`date_registration` desc LIMIT 0, 10";

        $qb = new QueryBuilder();
        $qb->select()
            ->from(['usuario' => 'user'])
            ->innerJoin(
                ['ua' => 'user_address'],
                'ua.id_user = usuario.id_user',
                []
            )
            ->innerJoin(
                ['endereco' => 'address'],
                'endereco.id_address = ua.id_address',
                ['address', 'number', 'postal_code', 'active']
            )
            ->innerJoin(
                'address_type',
                'address_type.id_address_type = endereco.id_address_type',
                ['type']
            )
            ->where('endereco.active = ?', 1)
            ->where('usuario.active = 1')
            ->where('(usuario.id_user = ? OR endereco.id_address = ?)', 2, 4)
            ->order('usuario.date_registration desc')
            ->limit(10);

        $result = md5($qb->sql()) == md5($expected);
        $this->assertTrue($result);
    }

    public function testDelete()
    {
        $expected = "DELETE FROM `user` WHERE (user.id_user = 10) ";
        
        $qb = new QueryBuilder();
        $qb->delete()
           ->from('user')
           ->where('user.id_user = ?', 10);
        
        $result = md5($qb->sql()) == md5($expected);
        $this->assertTrue($result);
    }
    
}

/* 
require_once __DIR__ . '/../vendor/autoload.php';

use \Osians\VeManager\QueryBuilderInterface;
use \Osians\VeManager\QueryBuilder;
use \Osians\VeManager\Database\Provider\Mysql;
use \Osians\VeManager\EntityInterface;
use \Osians\VeManager\Entity;
use \Osians\VeManager\VirtualEntity;
use \Osians\VeManager\VeManager;

# Conexao...
$driver = new Mysql();
$driver
    ->setHostname('localhost')
    ->setPort('3306')
    ->setUsername('wsantana')
    ->setPassword('123456')
    ->setDatabaseName('mdm');

$connection = $driver->conectar();

# query Builder
// $query = new QueryBuilder();
// $query->select()->from('usuario')->where('id_usuario = ?', 2);

# Virtual Entity Manager
$vem = new VeManager($connection);
// $entity = $vem->query($query)[0];

// $entity->setNome('Pia');
// $entity->setSbnome('Hasselhorst');
// $entity->setEmail('pia.hasselhorst@hotmail.com');


# criando uma nova entidade Usuario
# cria uma nova entidade a partir do banco de dados
// $entity = $vem->createEntity('usuario');

// $entity->setNome('Mario');
// $entity->setSbnome('Quintana');
// $entity->setEmail('mario@quintana.com.br');
// $entity->setAtivo(0);


// print_r($entity);

// $vem->save($entity);

// -- ----------------------------------------------
$query = new QueryBuilder();
$query
	->select()
	->from('usuario', ['*'])
	->innerJoin(['ue' => 'usuario_endereco'], 'ue.id_usuario = usuario.id_usuario AND ue.ativo = 1')
	->innerJoin('endereco', 'endereco.id_endereco = ue.id_endereco')
	->where('usuario.id_usuario = ?', 3);

die($query);

// $rs = $vem->query($query);
// print_r($rs);

// -- ----------------------------------------------


// -- ----------------------------------------------
// @todo precisa testar o Delete

$query = new QueryBuilder();
$query->delete()->from('usuario')->where('id_usuario = 20');

echo $query;

// -- ----------------------------------------------

// @todo precisa adicionar uma nova tabela endereços e tentar realizar as relations

*/

