<?php

namespace AuthLib\Tests\Auth;

use AuthLib\Auth\AuthService;
use AuthLib\Auth\ErrorCode;
use AuthLib\Auth\PasswordHasher;
use AuthLib\Auth\PasswordResetResult;
use AuthLib\Auth\PasswordResetResponse;
use AuthLib\Auth\SaltGenerator;
use AuthLib\DataStore\DataStoreInterface;
use AuthLib\DataStore\UserCredentials;
use AuthLib\Validation\InputValidatorInterface;
use PHPUnit\Framework\TestCase;

class PasswordResetTest extends TestCase
{
    private $dataStore;
    private $validator;
    private $passwordHasher;
    private $saltGenerator;
    private $authService;

    protected function setUp(): void
    {
        $this->dataStore = $this->createMock(DataStoreInterface::class);
        $this->validator = $this->createMock(InputValidatorInterface::class);
        $this->saltGenerator = $this->createMock(SaltGenerator::class);
        $this->passwordHasher = $this->createMock(PasswordHasher::class);
        $this->authService = new AuthService(
            $this->dataStore, 
            $this->validator, 
            $this->passwordHasher
        );
    }

    public function testRequestPasswordResetFailsWithEmptyUserId(): void
    {
        $result = $this->authService->requestPasswordReset('');
        
        $this->assertFalse($result->isSucceeded());
        $this->assertEquals(ErrorCode::VALIDATION_REQUIRED_FIELD_MISSING, $result->getErrorCode());
    }
    
    public function testRequestPasswordResetFailsWithNonExistentUser(): void
    {
        $this->dataStore->method('getUserCredentials')->willReturn(null);
        
        $result = $this->authService->requestPasswordReset('nonExistentUser');
        
        $this->assertFalse($result->isSucceeded());
        $this->assertEquals(ErrorCode::USER_NOT_FOUND, $result->getErrorCode());
    }
    
    public function testRequestPasswordResetSucceedsWithValidUser(): void
    {
        $userId = 'existingUser';
        $userCredentials = $this->createMock(UserCredentials::class);
        
        $this->dataStore->method('getUserCredentials')->willReturn($userCredentials);
        $this->dataStore->expects($this->once())
            ->method('storePasswordResetToken')
            ->willReturn(true);
        
        $result = $this->authService->requestPasswordReset($userId);
        
        $this->assertTrue($result->isSucceeded());
        $this->assertNotNull($result->getResetToken());
        $this->assertIsString($result->getResetToken());
    }
    
    public function testResetPasswordFailsWithMissingRequiredFields(): void
    {
        $this->validator->method('validateRequired')->willReturn(false);
        
        $result = $this->authService->resetPassword('userId', '', 'newPassword');
        
        $this->assertFalse($result->isSucceeded());
        $this->assertEquals(ErrorCode::VALIDATION_REQUIRED_FIELD_MISSING, $result->getErrorCode());
    }
    
    public function testResetPasswordFailsWithNonExistentUser(): void
    {
        $this->validator->method('validateRequired')->willReturn(true);
        $this->dataStore->method('getUserCredentials')->willReturn(null);
        
        $result = $this->authService->resetPassword('nonExistentUser', 'resetToken', 'newPassword');
        
        $this->assertFalse($result->isSucceeded());
        $this->assertEquals(ErrorCode::USER_NOT_FOUND, $result->getErrorCode());
    }
    
    public function testResetPasswordFailsWithInvalidToken(): void
    {
        $userId = 'existingUser';
        $resetToken = 'invalidToken';
        $newPassword = 'newPassword';
        
        $this->validator->method('validateRequired')->willReturn(true);
        
        $userCredentials = $this->createMock(UserCredentials::class);
        $this->dataStore->method('getUserCredentials')->willReturn($userCredentials);
        
        $this->dataStore->method('verifyPasswordResetToken')
            ->with($userId, $resetToken)
            ->willReturn(false);
        
        $result = $this->authService->resetPassword($userId, $resetToken, $newPassword);
        
        $this->assertFalse($result->isSucceeded());
        $this->assertEquals(ErrorCode::PASSWORD_RESET_INVALID_TOKEN, $result->getErrorCode());
    }
    
    public function testResetPasswordSucceedsWithValidToken(): void
    {
        $userId = 'existingUser';
        $resetToken = 'validToken';
        $newPassword = 'newPassword';
        $newHashedPassword = 'newHashedPassword';
        $newSalt = 'newSalt';
        
        $this->validator->method('validateRequired')->willReturn(true);
        
        $userCredentials = $this->createMock(UserCredentials::class);
        $this->dataStore->method('getUserCredentials')->willReturn($userCredentials);
        
        $this->dataStore->method('verifyPasswordResetToken')
            ->with($userId, $resetToken)
            ->willReturn(true);
        
        $this->saltGenerator->method('generateSalt')->willReturn($newSalt);
        
        $this->passwordHasher->method('hashPassword')
            ->with($newPassword, $newSalt)
            ->willReturn([
                'hashedPassword' => $newHashedPassword,
                'salt' => $newSalt
            ]);
        
        $this->dataStore->expects($this->once())
            ->method('updateUser')
            ->with($userId, $newHashedPassword, $newSalt)
            ->willReturn(true);
        
        $this->dataStore->expects($this->once())
            ->method('clearPasswordResetToken')
            ->with($userId)
            ->willReturn(true);
        
        $result = $this->authService->resetPassword($userId, $resetToken, $newPassword);
        
        $this->assertTrue($result->isSucceeded());
    }
}
