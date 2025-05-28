<?php

namespace AuthLib\DataStore;

use AuthLib\Config\ConfigReader;
use InvalidArgumentException;

/**
 * Factory for creating DataStore instances based on configuration
 */
class DataStoreFactory
{
    /**
     * @var array Registered data store types
     */
    private array $dataStores = [];
    
    /**
     * DataStoreFactory constructor
     */
    public function __construct()
    {
        // Register default data store types
        $this->registerDataStore('memory', MemoryDataStore::class);
        $this->registerDataStore('database', DatabaseDataStore::class);
    }
    
    /**
     * Register a data store type
     *
     * @param string $type Data store type identifier
     * @param string $className Fully qualified class name
     * @return void
     */
    public function registerDataStore(string $type, string $className): void
    {
        $this->dataStores[$type] = $className;
    }
    
    /**
     * Create a data store instance based on configuration
     *
     * @return DataStoreInterface
     * @throws InvalidArgumentException If data store type is invalid
     */
    public function createDataStore(): DataStoreInterface
    {
        $type = ConfigReader::getConfig('authlib.datastore.type', 'memory');
        
        if (!isset($this->dataStores[$type])) {
            throw new InvalidArgumentException("Invalid data store type: {$type}");
        }
        
        $className = $this->dataStores[$type];
        
        $config = ConfigReader::getConfigByPrefix("authlib.datastore.{$type}.");
        
        // If database type, also include database configuration
        if ($type === 'database') {
            $dbConfig = ConfigReader::getConfigByPrefix("authlib.datastore.database.");
            $config = array_merge($config, $dbConfig);
        }
        
        return new $className($config);
    }
}
