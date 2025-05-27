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
     * @var PasswordHasher
     */
    private PasswordHasher $passwordHasher;

    /**
     * AuthService constructor
     *
     * @param DataStoreInterface $dataStore
     * @param InputValidatorInterface $validator
     * @param PasswordHasher $passwordHasher
     */
    public function __construct(
        DataStoreInterface $dataStore,
        InputValidatorInterface $validator,
        PasswordHasher $passwordHasher
    ) {
        $this->dataStore = $dataStore;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * {@inheritdoc}
     */
    public function login(string $userId, string $password): AuthResult
    {
        $input = ['userId' => $userId, 'password' => $password];
        if (!$this->validator->validateRequired($input, ['userId', 'password'])) {
            return new AuthResult(false, 401);
        }

        $userCredentials = $this->dataStore->getUserCredentials($userId);
        if ($userCredentials === null) {
            return new AuthResult(false, 401);
        }

        $isValid = $this->passwordHasher->verifyPassword(
            $password,
            $userCredentials->getHashedPassword(),
            $userCredentials->getSalt()
        );

        return $isValid
            ? new AuthResult(true)
            : new AuthResult(false, 401);
    }
}
