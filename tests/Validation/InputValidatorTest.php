<?php

namespace AuthLib\Tests\Validation;

use AuthLib\Validation\InputValidator;
use PHPUnit\Framework\TestCase;

class InputValidatorTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new InputValidator();
    }

    public function testValidateRequiredSuccess(): void
    {
        $result = $this->validator->validateRequired(
            ['userId' => 'test', 'password' => 'test'], 
            ['userId', 'password']
        );
        
        $this->assertTrue($result);
    }
    
    public function testValidateRequiredFailureMissingField(): void
    {
        $result = $this->validator->validateRequired(
            ['userId' => 'test'], 
            ['userId', 'password']
        );
        
        $this->assertFalse($result);
    }
    
    public function testValidateRequiredFailureEmptyField(): void
    {
        $result = $this->validator->validateRequired(
            ['userId' => 'test', 'password' => ''], 
            ['userId', 'password']
        );
        
        $this->assertFalse($result);
    }
    
    public function testValidateRequiredFailureNullField(): void
    {
        $result = $this->validator->validateRequired(
            ['userId' => 'test', 'password' => null], 
            ['userId', 'password']
        );
        
        $this->assertFalse($result);
    }
}
