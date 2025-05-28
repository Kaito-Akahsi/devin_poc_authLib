<?php

namespace AuthLib\Tests\Config;

use AuthLib\Config\ConfigLoader;
use AuthLib\Config\ConfigReader;
use PHPUnit\Framework\TestCase;

class ConfigReaderTest extends TestCase
{
    private ConfigReader $configReader;
    
    protected function setUp(): void
    {
        $configLoader = new ConfigLoader();
        $this->configReader = new ConfigReader($configLoader);
    }
    
    public function testGetConfigWithDefaultValue(): void
    {
        $result = $this->configReader->getConfig('authlib.nonexistent.key', 'default_value');
        
        $this->assertEquals('default_value', $result);
    }
    
    public function testGetConfigByPrefix(): void
    {
        $this->configReader->clearTestValues();
        $this->configReader->setTestValue('authlib.test.key1', 'value1');
        $this->configReader->setTestValue('authlib.test.key2', 'value2');
        $this->configReader->setTestValue('other.key', 'other_value');
        
        try {
            $result = $this->configReader->getConfigByPrefix('authlib.test.');
            
            $this->assertIsArray($result);
            $this->assertArrayHasKey('authlib.test.key1', $result);
            $this->assertArrayHasKey('authlib.test.key2', $result);
            $this->assertArrayNotHasKey('other.key', $result);
            $this->assertEquals('value1', $result['authlib.test.key1']);
            $this->assertEquals('value2', $result['authlib.test.key2']);
        } finally {
            $this->configReader->clearTestValues();
        }
    }
}
