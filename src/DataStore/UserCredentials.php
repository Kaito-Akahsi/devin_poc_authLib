<?php

namespace AuthLib\DataStore;

/**
 * Class representing user credentials
 */
class UserCredentials
{
    /**
     * @var string User ID
     */
    private string $userId;

    /**
     * @var string Hashed password
     */
    private string $hashedPassword;

    /**
     * @var string Salt used for password hashing
     */
    private string $salt;

    /**
     * UserCredentials constructor
     *
     * @param string $userId User ID
     * @param string $hashedPassword Hashed password
     * @param string $salt Salt used for password hashing
     */
    public function __construct(string $userId, string $hashedPassword, string $salt)
    {
        $this->userId = $userId;
        $this->hashedPassword = $hashedPassword;
        $this->salt = $salt;
    }

    /**
     * Get user ID
     *
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Get hashed password
     *
     * @return string
     */
    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }
}
