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
    public function requestPasswordReset(string $userId): PasswordResetResult
    {
        if (empty($userId)) {
            return new PasswordResetResult(false, ErrorCode::VALIDATION_REQUIRED_FIELD_MISSING);
        }
        
        $userCredentials = $this->dataStore->getUserCredentials($userId);
        if ($userCredentials === null) {
            return new PasswordResetResult(false, ErrorCode::USER_NOT_FOUND);
        }
        
        // Generate a secure random token
        $resetToken = bin2hex(random_bytes(32));
        
        // Set expiration time (24 hours from now)
        $expiresAt = time() + 86400;
        
        // Store the token in the data store
        $success = $this->dataStore->storePasswordResetToken($userId, $resetToken, $expiresAt);
        
        if (!$success) {
            return new PasswordResetResult(false, ErrorCode::DATABASE_ERROR);
        }
        
        return new PasswordResetResult(true, null, null, $resetToken);
    }
    
    /**
     * {@inheritdoc}
     */
    public function resetPassword(string $userId, string $resetToken, string $newPassword): PasswordResetResponse
    {
        $input = [
            'userId' => $userId,
            'resetToken' => $resetToken,
            'newPassword' => $newPassword
        ];
        
        if (!$this->validator->validateRequired($input, ['userId', 'resetToken', 'newPassword'])) {
            return new PasswordResetResponse(false, ErrorCode::VALIDATION_REQUIRED_FIELD_MISSING);
        }
        
        $userCredentials = $this->dataStore->getUserCredentials($userId);
        if ($userCredentials === null) {
            return new PasswordResetResponse(false, ErrorCode::USER_NOT_FOUND);
        }
        
        // Verify the reset token
        $isValidToken = $this->dataStore->verifyPasswordResetToken($userId, $resetToken);
        if (!$isValidToken) {
            return new PasswordResetResponse(false, ErrorCode::PASSWORD_RESET_INVALID_TOKEN);
        }
        
        $hashResult = $this->passwordHasher->hashPassword($newPassword, 'newSalt');
        
        // Update the user's credentials
        $success = $this->dataStore->updateUser($userId, $hashResult['hashedPassword'], $hashResult['salt']);
        if (!$success) {
            return new PasswordResetResponse(false, ErrorCode::DATABASE_ERROR);
        }
        
        // Clear the reset token
        $this->dataStore->clearPasswordResetToken($userId);
        
        return new PasswordResetResponse(true);
    }
}
