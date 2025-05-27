<?php

namespace AuthLib\Config;

/**
 * Class for reading configuration values from php.ini
 */
class ConfigReader
{
    /**
     * Get a configuration value from php.ini
     *
     * @param string $key Configuration key
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value or default
     */
    public function getConfig(string $key, $default = null)
    {
        $value = ini_get($key);
        
        if ($value === false || $value === '') {
            return $default;
        }
        
        return $value;
    }
    
    /**
     * Get all configuration values with a specific prefix
     *
     * @param string $prefix Configuration key prefix
     * @return array Array of configuration values
     */
    public function getConfigByPrefix(string $prefix): array
    {
        $allConfig = ini_get_all(null, false);
        $result = [];
        
        foreach ($allConfig as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
}
