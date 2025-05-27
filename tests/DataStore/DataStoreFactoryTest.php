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
    private $configReader;
    private $factory;

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
            ->with('authlib.datastore.memory.')
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
        
        $this->factory->createDataStore();
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
            ->with('authlib.datastore.custom.')
            ->willReturn([]);
        
        try {
            $this->factory->createDataStore();
            $this->fail('Expected exception not thrown');
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
