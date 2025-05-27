<?php

namespace AuthLib\Tests\DataStore;

use AuthLib\DataStore\DatabaseDataStore;
use AuthLib\DataStore\UserCredentials;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class DatabaseDataStoreTest extends TestCase
{
    private $dataStore;
    private $mockPdo;
    private $mockStatement;

    protected function setUp(): void
    {
        $this->mockStatement = $this->createMock(PDOStatement::class);
        $this->mockPdo = $this->createMock(PDO::class);
        
        $this->dataStore = new DatabaseDataStore([
            'authlib.datastore.database.driver' => 'mysql',
            'authlib.datastore.database.host' => 'localhost',
            'authlib.datastore.database.port' => '3306',
            'authlib.datastore.database.dbname' => 'authlib',
            'authlib.datastore.database.username' => 'user',
            'authlib.datastore.database.password' => 'pass'
        ]);
        
        $reflectionProperty = new ReflectionProperty(DatabaseDataStore::class, 'connection');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->dataStore, $this->mockPdo);
    }

    public function testGetUserCredentialsReturnsUserWhenFound(): void
    {
        $userId = 'testUser';
        $hashedPassword = 'hashedPassword123';
        $salt = 'testSalt456';
        
        $this->mockStatement->method('fetch')->willReturn([
            'user_id' => $userId,
            'hashed_password' => $hashedPassword,
            'salt' => $salt,
            'created_at' => '2023-01-01 00:00:00',
            'updated_at' => '2023-01-01 00:00:00'
        ]);
        
        $this->mockPdo->method('prepare')->willReturn($this->mockStatement);
        
        $result = $this->dataStore->getUserCredentials($userId);
        
        $this->assertInstanceOf(UserCredentials::class, $result);
        $this->assertEquals($userId, $result->getUserId());
        $this->assertEquals($hashedPassword, $result->getHashedPassword());
        $this->assertEquals($salt, $result->getSalt());
    }
    
    public function testGetUserCredentialsReturnsNullWhenNotFound(): void
    {
        $this->mockStatement->method('fetch')->willReturn(false);
        $this->mockPdo->method('prepare')->willReturn($this->mockStatement);
        
        $result = $this->dataStore->getUserCredentials('nonExistentUser');
        
        $this->assertNull($result);
    }
    
    public function testAddUserExecutesCorrectStatement(): void
    {
        $userId = 'testUser';
        $hashedPassword = 'hashedPassword123';
        $salt = 'testSalt456';
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([
                'user_id' => $userId,
                'hashed_password' => $hashedPassword,
                'salt' => $salt
            ])
            ->willReturn(true);
        
        $this->mockPdo->method('prepare')->willReturn($this->mockStatement);
        
        $result = $this->dataStore->addUser($userId, $hashedPassword, $salt);
        
        $this->assertTrue($result);
    }
    
    public function testUpdateUserExecutesCorrectStatement(): void
    {
        $userId = 'testUser';
        $hashedPassword = 'newHashedPassword';
        $salt = 'newSalt';
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([
                'user_id' => $userId,
                'hashed_password' => $hashedPassword,
                'salt' => $salt
            ])
            ->willReturn(true);
        
        $this->mockPdo->method('prepare')->willReturn($this->mockStatement);
        
        $result = $this->dataStore->updateUser($userId, $hashedPassword, $salt);
        
        $this->assertTrue($result);
    }
    
    public function testDeleteUserExecutesCorrectStatement(): void
    {
        $userId = 'testUser';
        
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with(['user_id' => $userId])
            ->willReturn(true);
        
        $this->mockPdo->method('prepare')->willReturn($this->mockStatement);
        
        $result = $this->dataStore->deleteUser($userId);
        
        $this->assertTrue($result);
    }
    
    public function testCloseMethodResetsConnection(): void
    {
        $this->dataStore->close();
        
        $reflectionProperty = new ReflectionProperty(DatabaseDataStore::class, 'connection');
        $reflectionProperty->setAccessible(true);
        $connection = $reflectionProperty->getValue($this->dataStore);
        
        $this->assertNull($connection);
    }
}
