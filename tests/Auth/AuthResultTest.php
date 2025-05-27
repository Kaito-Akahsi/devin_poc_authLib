<?php

namespace AuthLib\Tests\Auth;

use AuthLib\Auth\AuthResult;
use AuthLib\Auth\ErrorCode;
use PHPUnit\Framework\TestCase;

class AuthResultTest extends TestCase
{
    public function testSuccessfulAuthResult(): void
    {
        $result = new AuthResult(true);
        
        $this->assertTrue($result->isSucceeded());
        $this->assertNull($result->getErrorCode());
        $this->assertNull($result->getErrorMessage());
    }
    
    public function testFailedAuthResultWithErrorCode(): void
    {
        $result = new AuthResult(false, ErrorCode::AUTH_FAILED);
        
        $this->assertFalse($result->isSucceeded());
        $this->assertEquals(ErrorCode::AUTH_FAILED, $result->getErrorCode());
        $this->assertEquals('Authentication failed', $result->getErrorMessage());
    }
    
    public function testFailedAuthResultWithCustomErrorMessage(): void
    {
        $customMessage = 'Custom error message';
        $result = new AuthResult(false, ErrorCode::AUTH_FAILED, $customMessage);
        
        $this->assertFalse($result->isSucceeded());
        $this->assertEquals(ErrorCode::AUTH_FAILED, $result->getErrorCode());
        $this->assertEquals($customMessage, $result->getErrorMessage());
    }
    
    public function testFailedAuthResultWithoutErrorCode(): void
    {
        $result = new AuthResult(false);
        
        $this->assertFalse($result->isSucceeded());
        $this->assertNull($result->getErrorCode());
        $this->assertNull($result->getErrorMessage());
    }
}
