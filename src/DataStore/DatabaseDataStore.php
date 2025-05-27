<?php

namespace AuthLib\DataStore;

/**
 * Stub implementation of data store using database storage
 * This is a placeholder for future implementation in Step 4
 */
class DatabaseDataStore implements DataStoreInterface
{
    /**
     * @var array Configuration options
     */
    private array $config;

    /**
     * DatabaseDataStore constructor
     *
     * @param array $config Configuration options
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserCredentials(string $userId): ?UserCredentials
    {
        return null;
    }
}
