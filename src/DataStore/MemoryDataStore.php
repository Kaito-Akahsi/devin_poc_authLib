<?php

namespace AuthLib\DataStore;

/**
 * Implementation of data store using in-memory storage
 */
class MemoryDataStore implements DataStoreInterface
{
    /**
     * @var array In-memory storage for user credentials
     */
    private array $users = [];
    
    /**
     * @var array In-memory storage for password reset tokens
     */
    private array $resetTokens = [];
    
    /**
     * @var array In-memory storage for user metadata
     */
    private array $userMetadata = [];
    
    /**
     * @var array Configuration options
     */
    private array $config;

    /**
     * MemoryDataStore constructor
     *
     * @param array $config Configuration options
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        
        if (isset($config['authlib.datastore.memory.init_test_data']) && 
            $config['authlib.datastore.memory.init_test_data'] === '1') {
            $this->initializeTestData();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addUser(string $userId, string $hashedPassword, string $salt): bool
    {
        $this->users[$userId] = new UserCredentials($userId, $hashedPassword, $salt);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserCredentials(string $userId): ?UserCredentials
    {
        return $this->users[$userId] ?? null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function updateUser(string $userId, string $hashedPassword, string $salt): bool
    {
        if (!isset($this->users[$userId])) {
            return false;
        }
        
        $this->users[$userId] = new UserCredentials($userId, $hashedPassword, $salt);
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function storePasswordResetToken(string $userId, string $resetToken, int $expiresAt): bool
    {
        if (!isset($this->users[$userId])) {
            return false;
        }
        
        $this->resetTokens[$userId] = [
            'token' => $resetToken,
            'expires_at' => $expiresAt
        ];
        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function verifyPasswordResetToken(string $userId, string $resetToken): bool
    {
        if (!isset($this->resetTokens[$userId])) {
            return false;
        }
        
        $tokenData = $this->resetTokens[$userId];
        
        if ($tokenData['expires_at'] < time()) {
            return false;
        }
        
        return $tokenData['token'] === $resetToken;
    }
    
    /**
     * {@inheritdoc}
     */
    public function clearPasswordResetToken(string $userId): bool
    {
        if (!isset($this->resetTokens[$userId])) {
            return false;
        }
        
        unset($this->resetTokens[$userId]);
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function storeUserMetadata(string $userId, string $key, $value): bool
    {
        if (!isset($this->users[$userId])) {
            return false;
        }
        
        if (!isset($this->userMetadata[$userId])) {
            $this->userMetadata[$userId] = [];
        }
        
        $this->userMetadata[$userId][$key] = $value;
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUserMetadata(string $userId, string $key)
    {
        if (!isset($this->userMetadata[$userId]) || !isset($this->userMetadata[$userId][$key])) {
            return null;
        }
        
        return $this->userMetadata[$userId][$key];
    }
    
    /**
     * Initialize test data for development/testing
     *
     * @return void
     */
    private function initializeTestData(): void
    {
        $this->addUser(
            'testuser',
            hash('sha256', 'password' . 'testsalt'),
            'testsalt'
        );
    }
}
