<?php

namespace AuthLib\Config;

use AuthLib\Auth\ErrorCode;
use InvalidArgumentException;

/**
 * Class for reading configuration values from php.ini
 */
class ConfigReader
{
    /**
     * @var array Default configuration values
     */
    private static array $defaults = [
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
    private static array $schema = [
        'authlib.datastore.type' => ['type' => 'enum', 'values' => ['memory', 'database']],
        'authlib.datastore.memory.init_test_data' => ['type' => 'boolean'],
        'authlib.datastore.database.driver' => ['type' => 'enum', 'values' => ['mysql', 'pgsql']],
        'authlib.datastore.database.port' => ['type' => 'integer', 'min' => 1, 'max' => 65535],
        'authlib.datastore.database.auto_connect' => ['type' => 'boolean']
    ];

    /**
     * Get a configuration value from php.ini
     *
     * @param string $key Configuration key
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value or default
     */
    public static function getConfig(string $key, $default = null)
    {
        $value = ini_get($key);
        
        if ($value === false || $value === '') {
            if ($default === null && isset(self::$defaults[$key])) {
                return self::$defaults[$key];
            }
            return $default;
        }
        
        if (isset(self::$schema[$key])) {
            self::validateConfigValue($key, $value);
        }
        
        return $value;
    }
    
    /**
     * Get all configuration values with a specific prefix
     *
     * @param string $prefix Configuration key prefix
     * @return array Array of configuration values
     */
    /**
     * For testing purposes - allows setting test values
     */
    private static $testValues = [];
    
    /**
     * Set a test value for unit testing
     * 
     * @param string $key Configuration key
     * @param mixed $value Configuration value
     */
    public static function setTestValue(string $key, $value): void
    {
        self::$testValues[$key] = $value;
    }
    
    /**
     * Clear all test values
     */
    public static function clearTestValues(): void
    {
        self::$testValues = [];
    }
    
    public static function getConfigByPrefix(string $prefix): array
    {
        $result = [];
        
        foreach (self::$testValues as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $result[$key] = $value;
            }
        }
        
        $allConfig = ini_get_all(null, false);
        foreach ($allConfig as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                if (isset(self::$schema[$key])) {
                    self::validateConfigValue($key, $value);
                }
                $result[$key] = $value;
            }
        }
        
        foreach (self::$defaults as $key => $value) {
            if (strpos($key, $prefix) === 0 && !isset($result[$key])) {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Validate a configuration value against its schema
     *
     * @param string $key Configuration key
     * @param mixed $value Configuration value
     * @throws InvalidArgumentException If validation fails
     */
    private static function validateConfigValue(string $key, $value): void
    {
        $schema = self::$schema[$key];
        
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
