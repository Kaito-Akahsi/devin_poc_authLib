<?php

namespace AuthLib\Tests\DataStore;

use AuthLib\DataStore\MemoryDataStore;
use AuthLib\DataStore\UserCredentials;
use PHPUnit\Framework\TestCase;

class MemoryDataStoreTest extends TestCase
{
    private $dataStore;

    protected function setUp(): void
    {
        $this->dataStore = new MemoryDataStore();
    }

    public function testGetUserCredentialsReturnsNullForNonExistentUser(): void
    {
        $result = $this->dataStore->getUserCredentials('nonExistentUser');
        
        $this->assertNull($result);
    }
    
    public function testAddUserAndGetUserCredentials(): void
    {
        $userId = 'testUser';
        $hashedPassword = 'hashedPassword123';
        $salt = 'testSalt456';
        
        $this->dataStore->addUser($userId, $hashedPassword, $salt);
        $result = $this->dataStore->getUserCredentials($userId);
        
        $this->assertInstanceOf(UserCredentials::class, $result);
        $this->assertEquals($userId, $result->getUserId());
        $this->assertEquals($hashedPassword, $result->getHashedPassword());
        $this->assertEquals($salt, $result->getSalt());
    }
    
    public function testConstructorWithTestDataConfig(): void
    {
        $config = ['authlib.datastore.memory.init_test_data' => '1'];
        $dataStore = new MemoryDataStore($config);
        
        $result = $dataStore->getUserCredentials('testuser');
        
        $this->assertInstanceOf(UserCredentials::class, $result);
        $this->assertEquals('testuser', $result->getUserId());
        $this->assertEquals(
            hash('sha256', 'password' . 'testsalt'),
            $result->getHashedPassword()
        );
        $this->assertEquals('testsalt', $result->getSalt());
    }
    
    public function testConstructorWithoutTestDataConfig(): void
    {
        $config = ['authlib.datastore.memory.other_option' => 'value'];
        $dataStore = new MemoryDataStore($config);
        
        $result = $dataStore->getUserCredentials('testuser');
        
        $this->assertNull($result);
    }
}
