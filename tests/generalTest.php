<?php

$ds = DIRECTORY_SEPARATOR;

require __DIR__ . "{$ds}..{$ds}src{$ds}Bootstrap.php";

class vitaGeneralTest extends PHPUnit\Framework\TestCase
{
    private $vita;

    public function setUp()
    {
        require_once __DIR__ . "/../src/System/Vita.php";
        $this->vita = Vita\System\Vita::getInstance();
        // $vita->log->write('Hallo Welt!');
    }

    public function testgetVitaInstance()
    {
        $this->assertInstanceOf('\Vita\System\Vita', $this->vita);
    }
    
    public function tearDown() {
    }
}

