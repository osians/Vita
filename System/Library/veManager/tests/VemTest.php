<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Osians\VeManager\VeManager;
use Osians\VeManager\QueryBuilder;

// PHPUnit test class
class VemTest extends TestCase
{
    private $_connection = ['localhost', '3306', 'wsantana', '123456', 'datamanager'];
    
    protected $vem = null;
    
    public function setUp()
    {
        $provider = new \Osians\VeManager\Database\Provider\Mysql();
        $provider
            ->setHostname($this->_connection[0])
            ->setPort($this->_connection[1])
            ->setUsername($this->_connection[2])
            ->setPassword($this->_connection[3])
            ->setDatabaseName($this->_connection[4]);
        
        $connection = $provider->connect();

        $this->vem = new VeManager($connection);
    }
    
    public function tearDown()
    {
    }
    
    public function testVirtualEntityCreate()
    {
        $vem = $this->vem;
        
        $user = $vem->create('user');
        $user->setId(16); // comment this line to make a insert instead of a update
        $user->setName('Jhonatan Doe');
        $user->setEmail('jhonatan.doe@gmail.com');
        $user->setAge(31);
        $user->setActive(1);

        //  Persist
        $vem->save($user);
        //$this->assertEquals('John Doe', $user->getName());
        $this->assertTrue(is_integer(intval($user->getId())));
    }
    
    public function testGetDatabaseRecordAndChange()
    {
        // create a Database Query
        $q = new QueryBuilder();
        $q->select()->from('user')->where("email = ?", "john.doe@hotmail.com");
        
        // use VeManager to exec the Query
        // this will return one VirtualEntity Model
        $user = $this->vem->fetchOne($q);
        if (empty($user)) {
            // 'Record does not exist'
            $this->assertTrue(true);
            return;
        }
        
        // change something
        $user->setEmail('jhonatan.doe@aol.com');

        // use VeManager to Persist data
        $this->vem->save($user);
        
        $this->assertTrue(!empty($user));
    }
    
    public function testDeleteRecord()
    {
        $user = $this->vem->create('user');
        $user->setId(10);
        //$this->assertTrue($this->vem->delete($user));
        $this->assertTrue(true);
    }
    
    public function testGetEntity()
    {
        $user = $this->vem->get('user', 11);
        $this->assertInstanceOf('\Osians\VeManager\VirtualEntity', $user);
    }
    
    public function testExecuteComplexQuery()
    {
        $query = new QueryBuilder();
        $query->select()->from('user')
              ->innerJoin(['ua' => 'user_address'], 'ua.id_user = user.id_user')
              ->innerJoin('address', 'address.id_address = ua.id_address', ['address', 'postal_code', 'number'])
              ->where('user.id_user = ?', 4);
        
        $record = $this->vem->fetchOne($query);
        var_dump($record->getPostalCode());
        $record->setPostalCode('22543002');
        var_dump($record->getPostalCode());
        $this->vem->save($record);
        var_dump($record->getPostalCode());
        // @todo
        $this->assertTrue(false);
    }
    
    public function testTwoDifferentEntitiesSameEm()
    {
        $query1 = new QueryBuilder;
        $query1->select()->from('user')->where('id_user = 4');
        $user = $this->vem->fetchOne($query1);
        $user->setName('W Santana Nasc');
        $this->vem->save($user);
        
        $query2 = new QueryBuilder;
        $query2->select()->from('address')->where('id_address = 1');
        $address = $this->vem->fetchOne($query2);
        $address->setPostalCode('99543002');
        
        $this->vem->save($address);
        
        //var_dump($user, $address); die();
        $this->assertTrue($user->getName() == 'W Santana Nasc' && $address->getPostalCode() == '99543002');
    }
}
