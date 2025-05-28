<?php

namespace AuthLib\Tests\Config;

use AuthLib\Config\ConfigLoader;
use AuthLib\Config\ConfigReader;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConfigReaderEnhancedTest extends TestCase
{
    private ConfigReader $configReader;
    
    protected function setUp(): void
    {
        $configLoader = new ConfigLoader();
        $this->configReader = new ConfigReader($configLoader);
    }
    
    public function testGetConfigReturnsDefaultValueWhenKeyNotFound(): void
    {
        $result = $this->configReader->getConfig('non.existent.key', 'default_value');
        $this->assertEquals('default_value', $result);
    }
    
    public function testGetConfigReturnsBuiltInDefaultWhenKeyNotFoundAndNoDefaultProvided(): void
    {
        $result = $this->configReader->getConfig('authlib.datastore.type');
        $this->assertEquals('memory', $result);
    }
    
    public function testGetConfigByPrefixIncludesDefaultValues(): void
    {
        $result = $this->configReader->getConfigByPrefix('authlib.datastore.');
        
        $this->assertArrayHasKey('authlib.datastore.type', $result);
        $this->assertEquals('memory', $result['authlib.datastore.type']);
        
        $this->assertArrayHasKey('authlib.datastore.database.host', $result);
        $this->assertEquals('localhost', $result['authlib.datastore.database.host']);
    }
    
    public function testValidationThrowsExceptionForInvalidEnumValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        $reflectionClass = new \ReflectionClass(ConfigReader::class);
        $method = $reflectionClass->getMethod('validateConfigValue');
        $method->setAccessible(true);
        
        $method->invokeArgs($this->configReader, ['authlib.datastore.type', 'invalid_type']);
    }
    
    public function testValidationThrowsExceptionForInvalidIntegerValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        $reflectionClass = new \ReflectionClass(ConfigReader::class);
        $method = $reflectionClass->getMethod('validateConfigValue');
        $method->setAccessible(true);
        
        $method->invokeArgs($this->configReader, ['authlib.datastore.database.port', 'not_a_number']);
    }
    
    public function testValidationThrowsExceptionForIntegerOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        $reflectionClass = new \ReflectionClass(ConfigReader::class);
        $method = $reflectionClass->getMethod('validateConfigValue');
        $method->setAccessible(true);
        
        $method->invokeArgs($this->configReader, ['authlib.datastore.database.port', '70000']);
    }
    
    public function testValidationThrowsExceptionForInvalidBooleanValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        $reflectionClass = new \ReflectionClass(ConfigReader::class);
        $method = $reflectionClass->getMethod('validateConfigValue');
        $method->setAccessible(true);
        
        $method->invokeArgs($this->configReader, ['authlib.datastore.memory.init_test_data', 'not_a_boolean']);
    }
    
    public function testValidationAcceptsValidValues(): void
    {
        $reflectionClass = new \ReflectionClass(ConfigReader::class);
        $method = $reflectionClass->getMethod('validateConfigValue');
        $method->setAccessible(true);
        
        $method->invokeArgs($this->configReader, ['authlib.datastore.type', 'memory']);
        $method->invokeArgs($this->configReader, ['authlib.datastore.type', 'database']);
        
        $method->invokeArgs($this->configReader, ['authlib.datastore.database.port', '3306']);
        $method->invokeArgs($this->configReader, ['authlib.datastore.database.port', '5432']);
        
        $method->invokeArgs($this->configReader, ['authlib.datastore.memory.init_test_data', '0']);
        $method->invokeArgs($this->configReader, ['authlib.datastore.memory.init_test_data', '1']);
        $method->invokeArgs($this->configReader, ['authlib.datastore.memory.init_test_data', 'true']);
        $method->invokeArgs($this->configReader, ['authlib.datastore.memory.init_test_data', 'false']);
        
        $this->assertTrue(true);
    }
}
