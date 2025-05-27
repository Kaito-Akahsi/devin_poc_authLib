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
}
