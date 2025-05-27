<?php

namespace AuthLib\Auth;

/**
 * Class for hashing and verifying passwords using SHA-256 with salt
 */
class PasswordHasher
{
    /**
     * @var SaltGenerator
     */
    private SaltGenerator $saltGenerator;

    /**
     * PasswordHasher constructor
     *
     * @param SaltGenerator $saltGenerator
     */
    public function __construct(SaltGenerator $saltGenerator)
    {
        $this->saltGenerator = $saltGenerator;
    }

    /**
     * Hash a password using SHA-256 with salt
     *
     * @param string $password Plain text password
     * @param string|null $salt Salt to use (generates new salt if null)
     * @return array Array containing hashed password and salt
     */
    public function hashPassword(string $password, ?string $salt = null): array
    {
        if ($salt === null) {
            $salt = $this->saltGenerator->generateSalt();
        }

        $hashedPassword = hash('sha256', $password . $salt);

        return [
            'hashedPassword' => $hashedPassword,
            'salt' => $salt
        ];
    }

    /**
     * Verify a password against a stored hash and salt
     *
     * @param string $password Plain text password to verify
     * @param string $storedHash Stored hash to compare against
     * @param string $salt Salt used for hashing
     * @return bool Whether the password is valid
     */
    public function verifyPassword(string $password, string $storedHash, string $salt): bool
    {
        $hashedPassword = hash('sha256', $password . $salt);
        
        return hash_equals($storedHash, $hashedPassword);
    }
}
