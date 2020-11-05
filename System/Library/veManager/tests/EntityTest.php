<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Osians\VeManager\VeManager;

// test Entity
class User extends Osians\VeManager\Entity
{
    protected $_name = null;
    protected $_age = null;
    protected $_email = null;
}

// PHPUnit test class
class EntityTest extends TestCase
{
    private $_connection = ['localhost', '3306', 'wsantana', '123456', 'datamanager'];
    
    /**
     * @var Osians\VeManager\VeManager 
     */
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
    
    public function testEntityCreate()
    {
        $entity = new User();
        $this->assertInstanceOf('\Osians\VeManager\Entity', $entity);
    }
    
    public function testPhysicalEntitySetData()
    {
        $user = new User();
        $user->setId(13);
        $user->setName('Diego da Silva');
        $user->setAge(28);
        $user->setEmail('luckshor@hotmail.com');

        $this->vem->save($user);
        
        $this->assertEquals('Diego da Silva', $user->getName());
//        $this->assertTrue(is_integer(intval($user->getId())));
    }
  
      public function testGetEntityRelation()
      {
          $ua = $this->vem->get('user_address', 1);
          $this->assertEquals('W Santana Nasc', $ua->getUser()->getName());
      }

      public function testSetEntityRelationID()
      {
          $ua = $this->vem->get('user_address', 1);
          $ua->setIdUser(2);
          $this->assertEquals('William Shakespeare de Souza', $ua->getUser()->getName());
      }

      public function testSetEntityRelationEntity()
      {
          $ua = $this->vem->get('user_address', 1);
          $user = $this->vem->get('user', 2);
          $ua->setUser($user);
          $this->assertEquals('William Shakespeare de Souza', $ua->getUser()->getName());
      }
      
    public function testEntityRelation()
    {
        $user = $this->vem->get('user', 4);
        // Bad
        //$postalCode = $user->getUserAddress()->getAddress()->getPostalCode();
        // Good
        $ua = $user->getUserAddress();
        $postalCode = $ua[0]->getAddress()->getPostalCode();
        $this->assertEquals('99543002', $postalCode);
    }
    
    public function tearDown()
    {
    }
}
