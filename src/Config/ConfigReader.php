<?php

namespace AuthLib\Config;

use AuthLib\Auth\ErrorCode;
use InvalidArgumentException;

/**
 * Class for reading configuration values
 */
class ConfigReader
{
    /**
     * @var array Default configuration values
     */
    private array $defaults = [
        'authlib.datastore.type' => 'memory',
        'authlib.datastore.memory.init_test_data' => '0',
        'authlib.datastore.database.driver' => 'mysql',
        'authlib.datastore.database.host' => 'localhost',
        'authlib.datastore.database.port' => '3306',
        'authlib.datastore.database.dbname' => 'authlib',
        'authlib.datastore.database.charset' => 'utf8mb4',
        'authlib.datastore.database.auto_connect' => '0'
    ];
    
    /**
     * @var array Configuration schema for validation
     */
    private array $schema = [
        'authlib.datastore.type' => ['type' => 'enum', 'values' => ['memory', 'database']],
        'authlib.datastore.memory.init_test_data' => ['type' => 'boolean'],
        'authlib.datastore.database.driver' => ['type' => 'enum', 'values' => ['mysql', 'pgsql']],
        'authlib.datastore.database.port' => ['type' => 'integer', 'min' => 1, 'max' => 65535],
        'authlib.datastore.database.auto_connect' => ['type' => 'boolean']
    ];
    
    /**
     * @var array Configuration values
     */
    private array $config = [];
    
    /**
     * @var array Test values for unit testing
     */
    private array $testValues = [];
    
    /**
     * ConfigReader constructor
     *
     * @param ConfigLoader $configLoader
     */
    public function __construct(ConfigLoader $configLoader = null)
    {
        if ($configLoader !== null) {
            $this->config = $configLoader->loadFromIniFile('php.ini');
        }
    }
    
    /**
     * Get a configuration value
     *
     * @param string $key Configuration key
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value or default
     */
    public function getConfig(string $key, $default = null)
    {
        if (isset($this->testValues[$key])) {
            return $this->testValues[$key];
        }
        
        if (isset($this->config[$key])) {
            $value = $this->config[$key];
            
            if (isset($this->schema[$key])) {
                $this->validateConfigValue($key, $value);
            }
            
            return $value;
        }
        
        if ($default === null && isset($this->defaults[$key])) {
            return $this->defaults[$key];
        }
        
        return $default;
    }
    
    /**
     * Get all configuration values with a specific prefix
     *
     * @param string $prefix Configuration key prefix
     * @return array Array of configuration values
     */
    public function getConfigByPrefix(string $prefix): array
    {
        $result = [];
        
        foreach ($this->testValues as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $result[$key] = $value;
            }
        }
        
        foreach ($this->config as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                if (isset($this->schema[$key])) {
                    $this->validateConfigValue($key, $value);
                }
                $result[$key] = $value;
            }
        }
        
        foreach ($this->defaults as $key => $value) {
            if (strpos($key, $prefix) === 0 && !isset($result[$key])) {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Set a test value for unit testing
     * 
     * @param string $key Configuration key
     * @param mixed $value Configuration value
     */
    public function setTestValue(string $key, $value): void
    {
        $this->testValues[$key] = $value;
    }
    
    /**
     * Clear all test values
     */
    public function clearTestValues(): void
    {
        $this->testValues = [];
    }
    
    /**
     * Validate a configuration value against its schema
     *
     * @param string $key Configuration key
     * @param mixed $value Configuration value
     * @throws InvalidArgumentException If validation fails
     */
    private function validateConfigValue(string $key, $value): void
    {
        $schema = $this->schema[$key];
        
        switch ($schema['type']) {
            case 'enum':
                if (!in_array($value, $schema['values'])) {
                    throw new InvalidArgumentException(
                        "Invalid value for {$key}: {$value}. " .
                        "Allowed values: " . implode(', ', $schema['values'])
                    );
                }
                break;
                
            case 'integer':
                if (!is_numeric($value) || (int)$value != $value) {
                    throw new InvalidArgumentException(
                        "Invalid value for {$key}: {$value}. Expected integer."
                    );
                }
                
                $intValue = (int)$value;
                if (isset($schema['min']) && $intValue < $schema['min']) {
                    throw new InvalidArgumentException(
                        "Invalid value for {$key}: {$value}. " .
                        "Minimum value: {$schema['min']}"
                    );
                }
                
                if (isset($schema['max']) && $intValue > $schema['max']) {
                    throw new InvalidArgumentException(
                        "Invalid value for {$key}: {$value}. " .
                        "Maximum value: {$schema['max']}"
                    );
                }
                break;
                
            case 'boolean':
                if ($value !== '0' && $value !== '1' && 
                    $value !== 'true' && $value !== 'false' && 
                    $value !== true && $value !== false) {
                    throw new InvalidArgumentException(
                        "Invalid value for {$key}: {$value}. " .
                        "Expected boolean (0, 1, true, false)."
                    );
                }
                break;
        }
    }
}
