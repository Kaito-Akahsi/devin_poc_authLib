<?php

namespace AuthLib\Tests\Config;

use AuthLib\Config\ConfigReader;
use PHPUnit\Framework\TestCase;

class ConfigReaderTest extends TestCase
{
    private $configReader;

    protected function setUp(): void
    {
        $this->configReader = new ConfigReader();
    }

    public function testGetConfigWithDefaultValue(): void
    {
        $result = $this->configReader->getConfig('authlib.nonexistent.key', 'default_value');
        
        $this->assertEquals('default_value', $result);
    }
    
    public function testGetConfigByPrefix(): void
    {
        ini_set('authlib.test.key1', 'value1');
        ini_set('authlib.test.key2', 'value2');
        ini_set('other.key', 'other_value');
        
        $result = $this->configReader->getConfigByPrefix('authlib.test.');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('authlib.test.key1', $result);
        $this->assertArrayHasKey('authlib.test.key2', $result);
        $this->assertArrayNotHasKey('other.key', $result);
        $this->assertEquals('value1', $result['authlib.test.key1']);
        $this->assertEquals('value2', $result['authlib.test.key2']);
    }
}
