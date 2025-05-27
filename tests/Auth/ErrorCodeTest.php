<?php

namespace AuthLib\Tests\Auth;

use AuthLib\Auth\ErrorCode;
use PHPUnit\Framework\TestCase;

class ErrorCodeTest extends TestCase
{
    public function testGetMessageReturnsCorrectMessages(): void
    {
        $this->assertEquals('No error', ErrorCode::getMessage(ErrorCode::NONE));
        $this->assertEquals('Authentication failed', ErrorCode::getMessage(ErrorCode::AUTH_FAILED));
        $this->assertEquals('User not found', ErrorCode::getMessage(ErrorCode::USER_NOT_FOUND));
        $this->assertEquals('Required field missing', ErrorCode::getMessage(ErrorCode::VALIDATION_REQUIRED_FIELD_MISSING));
        $this->assertEquals('Database connection error', ErrorCode::getMessage(ErrorCode::DATABASE_CONNECTION_ERROR));
        $this->assertEquals('Invalid reset token', ErrorCode::getMessage(ErrorCode::PASSWORD_RESET_INVALID_TOKEN));
        $this->assertEquals('SSO provider error', ErrorCode::getMessage(ErrorCode::SSO_PROVIDER_ERROR));
    }
    
    public function testGetMessageReturnsUnknownErrorForInvalidCode(): void
    {
        $this->assertEquals('Unknown error', ErrorCode::getMessage(999999));
    }
    
    public function testErrorCodeHierarchy(): void
    {
        $this->assertGreaterThan(ErrorCode::VALIDATION_ERROR, ErrorCode::VALIDATION_REQUIRED_FIELD_MISSING);
        $this->assertGreaterThan(ErrorCode::VALIDATION_ERROR, ErrorCode::VALIDATION_INVALID_FORMAT);
        
        $this->assertGreaterThan(ErrorCode::DATABASE_ERROR, ErrorCode::DATABASE_CONNECTION_ERROR);
        $this->assertGreaterThan(ErrorCode::DATABASE_ERROR, ErrorCode::DATABASE_QUERY_ERROR);
        
        $this->assertGreaterThan(ErrorCode::CONFIG_ERROR, ErrorCode::CONFIG_INVALID);
        $this->assertGreaterThan(ErrorCode::CONFIG_ERROR, ErrorCode::CONFIG_MISSING);
        
        $this->assertGreaterThan(ErrorCode::PASSWORD_RESET_ERROR, ErrorCode::PASSWORD_RESET_INVALID_TOKEN);
        $this->assertGreaterThan(ErrorCode::PASSWORD_RESET_ERROR, ErrorCode::PASSWORD_RESET_EXPIRED_TOKEN);
        
        $this->assertGreaterThan(ErrorCode::SSO_ERROR, ErrorCode::SSO_PROVIDER_ERROR);
        $this->assertGreaterThan(ErrorCode::SSO_ERROR, ErrorCode::SSO_CALLBACK_ERROR);
    }
}
