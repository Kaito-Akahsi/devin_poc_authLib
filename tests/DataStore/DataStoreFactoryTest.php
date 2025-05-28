<?php

namespace AuthLib\Tests\DataStore;

use AuthLib\Config\ConfigLoader;
use AuthLib\Config\ConfigReader;
use AuthLib\DataStore\DataStoreFactory;
use AuthLib\DataStore\DataStoreInterface;
use AuthLib\DataStore\MemoryDataStore;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataStoreFactoryTest extends TestCase
{
    private $factory;
    private $configReader;

    protected function setUp(): void
    {
        $this->configReader = $this->createMock(ConfigReader::class);
        $this->factory = new DataStoreFactory($this->configReader);
    }

    public function testCreateDataStoreWithDefaultType(): void
    {
        $this->configReader->method('getConfig')
            ->with('authlib.datastore.type', 'memory')
            ->willReturn('memory');
            
        $this->configReader->method('getConfigByPrefix')
            ->willReturn([]);
        
        $dataStore = $this->factory->createDataStore();
        
        $this->assertInstanceOf(DataStoreInterface::class, $dataStore);
        $this->assertInstanceOf(MemoryDataStore::class, $dataStore);
    }
    
    public function testCreateDataStoreWithInvalidType(): void
    {
        $this->configReader->method('getConfig')
            ->with('authlib.datastore.type', 'memory')
            ->willReturn('invalid_type');
            
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid data store type: invalid_type');
        
        $dataStore = $this->factory->createDataStore();
    }
    
    public function testRegisterAndCreateCustomDataStore(): void
    {
        $mockDataStore = $this->createMock(DataStoreInterface::class);
        $customDataStoreClass = get_class($mockDataStore);
        
        $this->factory->registerDataStore('custom', $customDataStoreClass);
        
        $this->configReader->method('getConfig')
            ->with('authlib.datastore.type', 'memory')
            ->willReturn('custom');
            
        $this->configReader->method('getConfigByPrefix')
            ->willReturn([]);
        
        $dataStore = $this->factory->createDataStore();
        
        $this->assertInstanceOf(DataStoreInterface::class, $dataStore);
        $this->assertInstanceOf($customDataStoreClass, $dataStore);
    }
}
