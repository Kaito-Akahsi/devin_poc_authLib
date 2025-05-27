<?php

namespace AuthLib\Tests\Auth;

use AuthLib\Auth\SaltGenerator;
use PHPUnit\Framework\TestCase;

class SaltGeneratorTest extends TestCase
{
    private $saltGenerator;

    protected function setUp(): void
    {
        $this->saltGenerator = new SaltGenerator();
    }

    public function testGenerateSaltReturnsString(): void
    {
        $salt = $this->saltGenerator->generateSalt();
        
        $this->assertIsString($salt);
    }
    
    public function testGenerateSaltHasCorrectLength(): void
    {
        $salt = $this->saltGenerator->generateSalt();
        
        $this->assertEquals(32, strlen($salt));
    }
    
    public function testGenerateSaltWithCustomLength(): void
    {
        $salt = $this->saltGenerator->generateSalt(8);
        
        $this->assertEquals(16, strlen($salt));
    }
    
    public function testGenerateSaltProducesRandomValues(): void
    {
        $salt1 = $this->saltGenerator->generateSalt();
        $salt2 = $this->saltGenerator->generateSalt();
        
        $this->assertNotEquals($salt1, $salt2);
    }
}
