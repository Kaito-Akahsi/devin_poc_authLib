<?php

namespace AuthLib\Auth;

/**
 * Class for generating random salts for password hashing
 */
class SaltGenerator
{
    /**
     * Generate a random salt string
     *
     * @param int $length Length of the salt string
     * @return string Random salt string
     */
    public function generateSalt(int $length = 16): string
    {
        $randomBytes = random_bytes($length);
        
        return bin2hex($randomBytes);
    }
}
