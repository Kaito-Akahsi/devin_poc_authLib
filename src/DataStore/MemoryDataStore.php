<?php

namespace AuthLib\DataStore;

/**
 * Stub implementation of data store using in-memory storage
 */
class MemoryDataStore implements DataStoreInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserCredentials(string $userId): ?array
    {
        return null;
    }
}
