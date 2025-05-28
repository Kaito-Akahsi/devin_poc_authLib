<?php

namespace AuthLib\Tests\Integration;

use AuthLib\Auth\AuthResult;
use AuthLib\Auth\AuthService;
use AuthLib\Auth\PasswordHasher;
use AuthLib\Auth\SaltGenerator;
use AuthLib\Config\ConfigLoader;
use AuthLib\Config\ConfigReader;
use AuthLib\DataStore\DataStoreFactory;
use AuthLib\DataStore\DatabaseDataStore;
use AuthLib\Validation\InputValidator;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for database functionality
 * 
 * Note: This test is marked as skipped by default since it requires
 * a real database connection. To run it, you need to:
 * 1. Set up a test database
 * 2. Configure the connection in config/authlib.ini
 * 3. Remove the markTestSkipped() call
 */
class DatabaseIntegrationTest extends TestCase
{
    private $authService;
    private $dataStore;
    private $configLoader;
    
    protected function setUp(): void
    {
        $this->markTestSkipped(
            'This test requires a real database connection. ' .
            'Configure the database in config/authlib.ini and remove this skip to run it.'
        );
        
        $this->configLoader = new ConfigLoader();
        
        $configFile = __DIR__ . '/../../config/authlib.ini';
        $this->configLoader->loadFromIniFile($configFile);
        
        ini_set('authlib.datastore.type', 'database');
        
        $dataStoreFactory = new DataStoreFactory();
        $this->dataStore = $dataStoreFactory->createDataStore();
        
        $this->assertInstanceOf(DatabaseDataStore::class, $this->dataStore);
        
        $validator = new InputValidator();
        $saltGenerator = new SaltGenerator();
        $passwordHasher = new PasswordHasher($saltGenerator);
        $this->authService = new AuthService($this->dataStore, $validator, $passwordHasher);
        
        try {
            $this->dataStore->deleteUser('testuser');
        } catch (\Exception $e) {
        }
    }
    
    protected function tearDown(): void
    {
        try {
            $this->dataStore->deleteUser('testuser');
        } catch (\Exception $e) {
        }
    }
    
    public function testDatabaseAuthenticationFlow(): void
    {
        $userId = 'testuser';
        $password = 'password123';
        
        $saltGenerator = new SaltGenerator();
        $passwordHasher = new PasswordHasher($saltGenerator);
        $hashResult = $passwordHasher->hashPassword($password);
        
        $this->dataStore->addUser($userId, $hashResult['hashedPassword'], $hashResult['salt']);
        
        $authResult = $this->authService->login($userId, $password);
        
        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertTrue($authResult->isSucceeded());
        $this->assertNull($authResult->getErrorCode());
        
        $authResult = $this->authService->login($userId, 'wrongpassword');
        
        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertFalse($authResult->isSucceeded());
        $this->assertEquals(401, $authResult->getErrorCode());
        
        $authResult = $this->authService->login('nonexistentuser', $password);
        
        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertFalse($authResult->isSucceeded());
        $this->assertEquals(401, $authResult->getErrorCode());
        
        $newPassword = 'newpassword456';
        $newHashResult = $passwordHasher->hashPassword($newPassword);
        
        $this->dataStore->updateUser(
            $userId, 
            $newHashResult['hashedPassword'], 
            $newHashResult['salt']
        );
        
        $authResult = $this->authService->login($userId, $password);
        
        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertFalse($authResult->isSucceeded());
        $this->assertEquals(401, $authResult->getErrorCode());
        
        $authResult = $this->authService->login($userId, $newPassword);
        
        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertTrue($authResult->isSucceeded());
        $this->assertNull($authResult->getErrorCode());
        
        $this->dataStore->deleteUser($userId);
        
        $authResult = $this->authService->login($userId, $newPassword);
        
        $this->assertInstanceOf(AuthResult::class, $authResult);
        $this->assertFalse($authResult->isSucceeded());
        $this->assertEquals(401, $authResult->getErrorCode());
    }
}
