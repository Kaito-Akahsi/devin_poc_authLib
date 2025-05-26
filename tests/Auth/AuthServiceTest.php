<?php

namespace AuthLib\Tests\Auth;

use AuthLib\Auth\AuthResult;
use AuthLib\Auth\AuthService;
use AuthLib\DataStore\DataStoreInterface;
use AuthLib\Validation\InputValidatorInterface;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    private $dataStore;
    private $validator;
    private $authService;

    protected function setUp(): void
    {
        $this->dataStore = $this->createMock(DataStoreInterface::class);
        $this->validator = $this->createMock(InputValidatorInterface::class);
        $this->authService = new AuthService($this->dataStore, $this->validator);
    }

    public function testLoginReturnsAuthResult(): void
    {
        $result = $this->authService->login('testUser', 'testPassword');
        
        $this->assertInstanceOf(AuthResult::class, $result);
    }
}
