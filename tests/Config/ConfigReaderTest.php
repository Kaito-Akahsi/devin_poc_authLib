<?php

namespace AuthLib\Tests\Config;

use AuthLib\Config\ConfigReader;
use PHPUnit\Framework\TestCase;

class ConfigReaderTest extends TestCase
{
    public function testGetConfigWithDefaultValue(): void
    {
        $result = ConfigReader::getConfig('authlib.nonexistent.key', 'default_value');
        
        $this->assertEquals('default_value', $result);
    }
    
    public function testGetConfigByPrefix(): void
    {
        ConfigReader::clearTestValues();
        ConfigReader::setTestValue('authlib.test.key1', 'value1');
        ConfigReader::setTestValue('authlib.test.key2', 'value2');
        ConfigReader::setTestValue('other.key', 'other_value');
        
        try {
            $result = ConfigReader::getConfigByPrefix('authlib.test.');
            
            $this->assertIsArray($result);
            $this->assertArrayHasKey('authlib.test.key1', $result);
            $this->assertArrayHasKey('authlib.test.key2', $result);
            $this->assertArrayNotHasKey('other.key', $result);
            $this->assertEquals('value1', $result['authlib.test.key1']);
            $this->assertEquals('value2', $result['authlib.test.key2']);
        } finally {
            ConfigReader::clearTestValues();
        }
    }
}
