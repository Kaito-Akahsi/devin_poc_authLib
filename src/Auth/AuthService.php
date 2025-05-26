<?php

namespace AuthLib\Auth;

use AuthLib\DataStore\DataStoreInterface;
use AuthLib\Validation\InputValidatorInterface;

/**
 * Stub implementation of the authentication service
 */
class AuthService implements AuthInterface
{
    /**
     * @var DataStoreInterface
     */
    private DataStoreInterface $dataStore;

    /**
     * @var InputValidatorInterface
     */
    private InputValidatorInterface $validator;

    /**
     * AuthService constructor
     *
     * @param DataStoreInterface $dataStore
     * @param InputValidatorInterface $validator
     */
    public function __construct(
        DataStoreInterface $dataStore,
        InputValidatorInterface $validator
    ) {
        $this->dataStore = $dataStore;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function login(string $userId, string $password): AuthResult
    {
        return new AuthResult(false, 401);
    }
}
