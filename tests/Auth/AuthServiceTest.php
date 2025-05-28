<?php

namespace AuthLib\Tests\Auth;

use AuthLib\Auth\AuthResult;
use AuthLib\Auth\AuthService;
use AuthLib\Auth\ErrorCode;
use AuthLib\Auth\PasswordHasher;
use AuthLib\DataStore\DataStoreInterface;
use AuthLib\DataStore\UserCredentials;
use AuthLib\Validation\InputValidatorInterface;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    private $dataStore;
    private $validator;
    private $passwordHasher;
    private $authService;

    protected function setUp(): void
    {
        $this->dataStore = $this->createMock(DataStoreInterface::class);
        $this->validator = $this->createMock(InputValidatorInterface::class);
        $this->passwordHasher = $this->createMock(PasswordHasher::class);
        $this->authService = new AuthService(
            $this->dataStore, 
            $this->validator, 
            $this->passwordHasher
        );
    }

    public function testLoginReturnsAuthResult(): void
    {
        $this->validator->method('validateRequired')->willReturn(true);
        $this->dataStore->method('getUserCredentials')->willReturn(null);
        
        $result = $this->authService->login('testUser', 'testPassword');
        
        $this->assertInstanceOf(AuthResult::class, $result);
    }
    
    public function testLoginFailsWithInvalidInput(): void
    {
        $this->validator->method('validateRequired')->willReturn(false);
        
        $result = $this->authService->login('', '');
        
        $this->assertFalse($result->isSucceeded());
        $this->assertEquals(ErrorCode::VALIDATION_REQUIRED_FIELD_MISSING, $result->getErrorCode());
    }
    
    public function testLoginFailsWithNonExistentUser(): void
    {
        $this->validator->method('validateRequired')->willReturn(true);
        $this->dataStore->method('getUserCredentials')->willReturn(null);
        
        $result = $this->authService->login('nonExistentUser', 'password');
        
        $this->assertFalse($result->isSucceeded());
        $this->assertEquals(ErrorCode::USER_NOT_FOUND, $result->getErrorCode());
    }
    
    public function testLoginFailsWithInvalidPassword(): void
    {
        $this->validator->method('validateRequired')->willReturn(true);
        
        $userCredentials = $this->createMock(UserCredentials::class);
        $userCredentials->method('getHashedPassword')->willReturn('hashedPassword');
        $userCredentials->method('getSalt')->willReturn('salt');
        
        $this->dataStore->method('getUserCredentials')->willReturn($userCredentials);
        $this->passwordHasher->method('verifyPassword')->willReturn(false);
        
        $result = $this->authService->login('existingUser', 'wrongPassword');
        
        $this->assertFalse($result->isSucceeded());
        $this->assertEquals(ErrorCode::AUTH_FAILED, $result->getErrorCode());
    }
    
    public function testLoginSucceedsWithValidCredentials(): void
    {
        $this->validator->method('validateRequired')->willReturn(true);
        
        $userCredentials = $this->createMock(UserCredentials::class);
        $userCredentials->method('getHashedPassword')->willReturn('hashedPassword');
        $userCredentials->method('getSalt')->willReturn('salt');
        
        $this->dataStore->method('getUserCredentials')->willReturn($userCredentials);
        $this->passwordHasher->method('verifyPassword')->willReturn(true);
        
        $result = $this->authService->login('existingUser', 'correctPassword');
        
        $this->assertTrue($result->isSucceeded());
        $this->assertNull($result->getErrorCode());
    }
}
