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
}
