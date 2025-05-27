<?php

namespace AuthLib\Tests\Auth;

use AuthLib\Auth\PasswordHasher;
use AuthLib\Auth\SaltGenerator;
use PHPUnit\Framework\TestCase;

class PasswordHasherTest extends TestCase
{
    private $passwordHasher;
    private $saltGenerator;

    protected function setUp(): void
    {
        $this->saltGenerator = $this->createMock(SaltGenerator::class);
        $this->saltGenerator->method('generateSalt')->willReturn('testsalt123456789');
        
        $this->passwordHasher = new PasswordHasher($this->saltGenerator);
    }

    public function testHashPasswordWithGeneratedSalt(): void
    {
        $result = $this->passwordHasher->hashPassword('password123');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('hashedPassword', $result);
        $this->assertArrayHasKey('salt', $result);
        $this->assertEquals('testsalt123456789', $result['salt']);
        $this->assertEquals(
            hash('sha256', 'password123' . 'testsalt123456789'),
            $result['hashedPassword']
        );
    }
    
    public function testHashPasswordWithProvidedSalt(): void
    {
        $result = $this->passwordHasher->hashPassword('password123', 'providedsalt');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('hashedPassword', $result);
        $this->assertArrayHasKey('salt', $result);
        $this->assertEquals('providedsalt', $result['salt']);
        $this->assertEquals(
            hash('sha256', 'password123' . 'providedsalt'),
            $result['hashedPassword']
        );
    }
    
    public function testVerifyPasswordSuccess(): void
    {
        $password = 'password123';
        $salt = 'testsalt';
        $hashedPassword = hash('sha256', $password . $salt);
        
        $result = $this->passwordHasher->verifyPassword($password, $hashedPassword, $salt);
        
        $this->assertTrue($result);
    }
    
    public function testVerifyPasswordFailure(): void
    {
        $password = 'password123';
        $wrongPassword = 'wrongpassword';
        $salt = 'testsalt';
        $hashedPassword = hash('sha256', $password . $salt);
        
        $result = $this->passwordHasher->verifyPassword($wrongPassword, $hashedPassword, $salt);
        
        $this->assertFalse($result);
    }
}
