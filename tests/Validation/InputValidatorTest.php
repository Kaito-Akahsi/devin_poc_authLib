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

    public function testValidateRequired(): void
    {
        $result = $this->validator->validateRequired(['userId' => 'test', 'password' => 'test'], ['userId', 'password']);
        
        $this->assertTrue($result);
    }
}
