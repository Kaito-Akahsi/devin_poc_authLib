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
     * @return array Configuration values loaded from the file
     */
    public function loadFromIniFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }
        
        try {
            $config = parse_ini_file($filePath, false, INI_SCANNER_NORMAL);
            
            if ($config === false) {
                return [];
            }
        } catch (\Throwable $e) {
            return [];
        }
        
        return $config;
    }
}
