<?php

namespace AuthLib\Auth;

use AuthLib\DataStore\DataStoreInterface;
use AuthLib\Validation\InputValidatorInterface;

/**
 * Implementation of the authentication service
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
            return new AuthResult(false, ErrorCode::VALIDATION_REQUIRED_FIELD_MISSING);
        }

        $userCredentials = $this->dataStore->getUserCredentials($userId);
        if ($userCredentials === null) {
            return new AuthResult(false, ErrorCode::USER_NOT_FOUND);
        }

        $isValid = $this->passwordHasher->verifyPassword(
            $password,
            $userCredentials->getHashedPassword(),
            $userCredentials->getSalt()
        );

        return $isValid
            ? new AuthResult(true)
            : new AuthResult(false, ErrorCode::AUTH_FAILED);
    }
    
    /**
     * {@inheritdoc}
     */
    public function requestPasswordReset(string $userId): AuthResult
    {
        if (empty($userId)) {
            return new AuthResult(false, ErrorCode::VALIDATION_REQUIRED_FIELD_MISSING);
        }
        
        $userCredentials = $this->dataStore->getUserCredentials($userId);
        if ($userCredentials === null) {
            return new AuthResult(false, ErrorCode::USER_NOT_FOUND);
        }
        
        
        return new AuthResult(true);
    }
    
    /**
     * {@inheritdoc}
     */
    public function resetPassword(string $userId, string $resetToken, string $newPassword): AuthResult
    {
        $input = [
            'userId' => $userId,
            'resetToken' => $resetToken,
            'newPassword' => $newPassword
        ];
        
        if (!$this->validator->validateRequired($input, ['userId', 'resetToken', 'newPassword'])) {
            return new AuthResult(false, ErrorCode::VALIDATION_REQUIRED_FIELD_MISSING);
        }
        
        $userCredentials = $this->dataStore->getUserCredentials($userId);
        if ($userCredentials === null) {
            return new AuthResult(false, ErrorCode::USER_NOT_FOUND);
        }
        
        
        return new AuthResult(true);
    }
}
