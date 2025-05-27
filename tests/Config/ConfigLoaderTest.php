<?php

namespace AuthLib\Tests\Config;

use AuthLib\Config\ConfigLoader;
use AuthLib\Config\ConfigReader;
use PHPUnit\Framework\TestCase;

class ConfigLoaderTest extends TestCase
{
    private $configReader;
    private $configLoader;
    private $tempFile;

    protected function setUp(): void
    {
        $this->configReader = $this->createMock(ConfigReader::class);
        $this->configLoader = new ConfigLoader($this->configReader);
        
        $this->tempFile = tempnam(sys_get_temp_dir(), 'authlib_test_');
        file_put_contents($this->tempFile, "authlib.test.key = value\nauthlib.datastore.type = memory");
    }
    
    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testLoadFromIniFileSuccess(): void
    {
        $result = $this->configLoader->loadFromIniFile($this->tempFile);
        
        $this->assertTrue($result);
    }
    
    public function testLoadFromIniFileNonExistentFile(): void
    {
        $result = $this->configLoader->loadFromIniFile('/non/existent/file.ini');
        
        $this->assertFalse($result);
    }
    
    public function testLoadFromIniFileInvalidFile(): void
    {
        $invalidFile = tempnam(sys_get_temp_dir(), 'authlib_invalid_');
        file_put_contents($invalidFile, "invalid ini content\n=====");
        
        $result = $this->configLoader->loadFromIniFile($invalidFile);
        
        $this->assertFalse($result);
        
        if (file_exists($invalidFile)) {
            unlink($invalidFile);
        }
    }
}
