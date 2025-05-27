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
     * Add user credentials to the in-memory store
     *
     * @param string $userId User ID
     * @param string $hashedPassword Hashed password
     * @param string $salt Salt used for hashing
     * @return void
     */
    public function addUser(string $userId, string $hashedPassword, string $salt): void
    {
        $this->users[$userId] = new UserCredentials($userId, $hashedPassword, $salt);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserCredentials(string $userId): ?UserCredentials
    {
        return $this->users[$userId] ?? null;
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
