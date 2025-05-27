<?php

namespace AuthLib\Tests\DataStore;

use AuthLib\DataStore\DatabaseDataStore;
use PHPUnit\Framework\TestCase;

class DatabaseDataStoreTest extends TestCase
{
    private $dataStore;

    protected function setUp(): void
    {
        $this->dataStore = new DatabaseDataStore();
    }

    public function testGetUserCredentialsReturnsNullForStubImplementation(): void
    {
        $result = $this->dataStore->getUserCredentials('anyUser');
        
        $this->assertNull($result);
    }
    
    public function testConstructorWithConfig(): void
    {
        $config = [
            'authlib.datastore.database.host' => 'localhost',
            'authlib.datastore.database.port' => '3306',
            'authlib.datastore.database.dbname' => 'authlib',
            'authlib.datastore.database.username' => 'user',
            'authlib.datastore.database.password' => 'pass'
        ];
        
        $dataStore = new DatabaseDataStore($config);
        
        $this->assertInstanceOf(DatabaseDataStore::class, $dataStore);
    }
}
