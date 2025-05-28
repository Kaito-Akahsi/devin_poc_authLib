<?php

namespace AuthLib\DataStore;

/**
 * Interface for data store connections
 */
interface DataStoreInterface
{
    /**
     * Get user credentials by user ID
     *
     * @param string $userId User ID
     * @return UserCredentials|null User credentials or null if not found
     */
    public function getUserCredentials(string $userId): ?UserCredentials;
    
    /**
     * Update user credentials
     *
     * @param string $userId User ID
     * @param string $hashedPassword Hashed password
     * @param string $salt Salt used for hashing
     * @return bool Whether the operation was successful
     */
    public function updateUser(string $userId, string $hashedPassword, string $salt): bool;


    /**
     * Add a new user to the database
     *
     * @param string $userId User ID
     * @param string $hashedPassword Hashed password
     * @param string $salt Salt used for hashing
     * @return bool Whether the operation was successful
     */
    public function addUser(string $userId, string $hashedPassword, string $salt): bool;
    
    /**
     * Store a password reset token for a user
     *
     * @param string $userId User ID
     * @param string $resetToken Reset token
     * @param int $expiresAt Expiration timestamp
     * @return bool Whether the operation was successful
     */
    public function storePasswordResetToken(string $userId, string $resetToken, int $expiresAt): bool;
    
    /**
     * Verify a password reset token for a user
     *
     * @param string $userId User ID
     * @param string $resetToken Reset token
     * @return bool Whether the token is valid
     */
    public function verifyPasswordResetToken(string $userId, string $resetToken): bool;
    
    /**
     * Clear a password reset token for a user
     *
     * @param string $userId User ID
     * @return bool Whether the operation was successful
     */
    public function clearPasswordResetToken(string $userId): bool;
    
    /**
     * Store user metadata
     *
     * @param string $userId User ID
     * @param string $key Metadata key
     * @param mixed $value Metadata value
     * @return bool Whether the operation was successful
     */
    public function storeUserMetadata(string $userId, string $key, $value): bool;
    
    /**
     * Get user metadata
     *
     * @param string $userId User ID
     * @param string $key Metadata key
     * @return mixed|null Metadata value or null if not found
     */
    public function getUserMetadata(string $userId, string $key);
}
