<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class InstanceTest extends TestCase
{
    private $_connection = ['localhost', '3306', 'wsantana', '123456', 'datamanager'];

    public function setUp()
    {
    }

    public function testMysqlProviderSetUp()
    {
        $provider = new \Osians\VeManager\Database\Provider\Mysql();
        $provider
            ->setHostname($this->_connection[0])
            ->setPort($this->_connection[1])
            ->setUsername($this->_connection[2])
            ->setPassword($this->_connection[3])
            ->setDatabaseName($this->_connection[4]);
        
        $connection = $provider->connect();
        
        $this->assertInstanceOf('\PDO', $connection);
    }
    
    public function testVeManagerCreateInstanceAndSetOnConstruct()
    {
        $provider = new \Osians\VeManager\Database\Provider\Mysql();
        $provider
            ->setHostname($this->_connection[0])
            ->setPort($this->_connection[1])
            ->setUsername($this->_connection[2])
            ->setPassword($this->_connection[3])
            ->setDatabaseName($this->_connection[4]);
        
        $connection = $provider->connect();
        
        $this->assertInstanceOf('\PDO', $connection);
        
        $vem = new \Osians\VeManager\VeManager($connection);
        $this->assertInstanceOf('\Osians\VeManager\VeManager', $vem);
    }
    
    public function testQueryBuilderCreate()
    {
        $select = new Osians\VeManager\QueryBuilder();
        $this->assertInstanceOf('\Osians\VeManager\QueryBuilder', $select);
    }

    public function testVirtualEntityCreate()
    {
        $entity = new Osians\VeManager\VirtualEntity();
        $this->assertInstanceOf('\Osians\VeManager\VirtualEntity', $entity);        
    }
    
    public function tearDown()
    {
    }
}

