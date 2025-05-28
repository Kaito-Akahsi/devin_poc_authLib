<?php

namespace AuthLib\Auth;

/**
 * Interface for Single Sign-On (SSO) integration
 */
interface SsoInterface
{
    /**
     * Initialize SSO authentication flow
     * 
     * @param string $provider SSO provider identifier
     * @param array $options Provider-specific options
     * @return string Redirect URL for SSO authentication
     */
    public function initiateSsoAuth(string $provider, array $options = []): string;
    
    /**
     * Process SSO authentication callback
     * 
     * @param string $provider SSO provider identifier
     * @param array $callbackData Data received from SSO provider callback
     * @return AuthResult Authentication result
     */
    public function processSsoCallback(string $provider, array $callbackData): AuthResult;
    
    /**
     * Link an existing user account with SSO provider
     * 
     * @param string $userId User ID
     * @param string $provider SSO provider identifier
     * @param string $providerUserId User ID from the SSO provider
     * @return bool Whether the linking was successful
     */
    public function linkUserWithSsoProvider(string $userId, string $provider, string $providerUserId): bool;
    
    /**
     * Get list of SSO providers linked to a user
     * 
     * @param string $userId User ID
     * @return array List of linked SSO providers
     */
    public function getLinkedSsoProviders(string $userId): array;
}
