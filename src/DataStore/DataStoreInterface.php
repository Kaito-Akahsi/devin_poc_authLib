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
}
