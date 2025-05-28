<?php

namespace AuthLib\Auth;

/**
 * Interface for authentication services
 */
interface AuthInterface
{
    /**
     * Authenticate a user with userId and password
     *
     * @param string $userId User ID
     * @param string $password Password
     * @return AuthResult Authentication result
     */
    public function login(string $userId, string $password): AuthResult;
    
    /**
     * Request a password reset for a user
     * 
     * @param string $userId User ID
     * @return PasswordResetResult Result of the password reset request
     */
    public function requestPasswordReset(string $userId): PasswordResetResult;
    
    /**
     * Reset a user's password using a reset token
     * 
     * @param string $userId User ID
     * @param string $resetToken Password reset token
     * @param string $newPassword New password
     * @return PasswordResetResponse Result of the password reset operation
     */
    public function resetPassword(string $userId, string $resetToken, string $newPassword): PasswordResetResponse;
    
    /**
     * Add a new user to the system
     * 
     * @param string $userId User ID
     * @param string $password Password
     * @return AuthResult Result of the user addition operation
     */
    public function addUser(string $userId, string $password): AuthResult;
}
