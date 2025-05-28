<?php

namespace AuthLib\Tests\DataStore;

use AuthLib\Config\ConfigReader;
use AuthLib\DataStore\DataStoreFactory;
use AuthLib\DataStore\DataStoreInterface;
use AuthLib\DataStore\MemoryDataStore;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataStoreFactoryTest extends TestCase
{
    private $factory;
    private $originalGetConfig;
    private $originalGetConfigByPrefix;
    private $configValues = [];
    private $configPrefixValues = [];

    protected function setUp(): void
    {
        $this->factory = new DataStoreFactory();
        
        $this->originalGetConfig = function_exists('AuthLib\Config\ConfigReader::getConfig') 
            ? 'AuthLib\Config\ConfigReader::getConfig' 
            : null;
        $this->originalGetConfigByPrefix = function_exists('AuthLib\Config\ConfigReader::getConfigByPrefix') 
            ? 'AuthLib\Config\ConfigReader::getConfigByPrefix' 
            : null;
            
        $this->configValues = [
            'authlib.datastore.type' => 'memory'
        ];
        
        $this->configPrefixValues = [
            'authlib.datastore.memory.' => [],
            'authlib.datastore.database.' => [],
            'authlib.datastore.custom.' => []
        ];
        
        $this->setUpStaticMethodOverrides();
    }
    
    protected function tearDown(): void
    {
        $this->restoreStaticMethodOverrides();
    }
    
    private function setUpStaticMethodOverrides()
    {
        
    }
    
    private function restoreStaticMethodOverrides()
    {
    }

    public function testCreateDataStoreWithDefaultType(): void
    {
        $this->configValues['authlib.datastore.type'] = 'memory';
        
        $dataStore = $this->factory->createDataStore();
        
        $this->assertInstanceOf(DataStoreInterface::class, $dataStore);
        $this->assertInstanceOf(MemoryDataStore::class, $dataStore);
    }
    
    public function testCreateDataStoreWithInvalidType(): void
    {
        
        $this->factory->registerDataStore('valid_type', MemoryDataStore::class);
        
        
        $this->markTestSkipped('Cannot easily test with static methods');
    }
    
    public function testRegisterAndCreateCustomDataStore(): void
    {
        $mockDataStore = $this->createMock(DataStoreInterface::class);
        $customDataStoreClass = get_class($mockDataStore);
        
        $this->factory->registerDataStore('custom', $customDataStoreClass);
        
        $this->markTestSkipped('Cannot easily test with static methods');
    }
}
