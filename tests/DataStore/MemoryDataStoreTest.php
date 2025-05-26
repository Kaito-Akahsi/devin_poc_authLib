<?php

namespace AuthLib\Tests\DataStore;

use AuthLib\DataStore\MemoryDataStore;
use PHPUnit\Framework\TestCase;

class MemoryDataStoreTest extends TestCase
{
    private $dataStore;

    protected function setUp(): void
    {
        $this->dataStore = new MemoryDataStore();
    }

    public function testGetUserCredentialsReturnsNullForNonExistentUser(): void
    {
        $result = $this->dataStore->getUserCredentials('nonExistentUser');
        
        $this->assertNull($result);
    }
}
