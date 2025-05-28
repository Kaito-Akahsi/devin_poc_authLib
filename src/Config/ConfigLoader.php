<?php

namespace AuthLib\Config;

/**
 * Class for loading configuration from files
 */
class ConfigLoader
{
    /**
     * ConfigLoader constructor
     */
    public function __construct()
    {
    }
    
    /**
     * Load configuration from an INI file
     *
     * @param string $filePath Path to INI file
     * @return bool Whether the file was successfully loaded
     */
    public function loadFromIniFile(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }
        
        try {
            $config = parse_ini_file($filePath, false, INI_SCANNER_NORMAL);
            
            if ($config === false) {
                return false;
            }
        } catch (\Throwable $e) {
            return false;
        }
        
        foreach ($config as $key => $value) {
            ini_set($key, $value);
        }
        
        return true;
    }
}
