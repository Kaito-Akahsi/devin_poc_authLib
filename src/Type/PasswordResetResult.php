<?php

namespace AuthLib\Auth;

/**
 * Class representing the result of a password reset request
 */
class PasswordResetResult
{
    /**
     * @var bool Whether the password reset request was successful
     */
    private bool $isSucceeded;

    /**
     * @var int|null Error code if request failed
     */
    private ?int $errorCode;
    
    /**
     * @var string|null Error message if request failed
     */
    private ?string $errorMessage;

    /**
     * @var string|null Reset token if request succeeded
     */
    private ?string $resetToken;

    /**
     * PasswordResetResult constructor
     *
     * @param bool $isSucceeded Whether request succeeded
     * @param int|null $errorCode Error code if request failed
     * @param string|null $errorMessage Custom error message (optional)
     * @param string|null $resetToken Reset token if request succeeded
     */
    public function __construct(bool $isSucceeded, ?int $errorCode = null, ?string $errorMessage = null, ?string $resetToken = null)
    {
        $this->isSucceeded = $isSucceeded;
        $this->errorCode = $errorCode;
        $this->resetToken = $resetToken;
        
        if ($errorMessage === null && $errorCode !== null) {
            $this->errorMessage = ErrorCode::getMessage($errorCode);
        } else {
            $this->errorMessage = $errorMessage;
        }
    }

    /**
     * Get whether request succeeded
     *
     * @return bool
     */
    public function isSucceeded(): bool
    {
        return $this->isSucceeded;
    }

    /**
     * Get error code if request failed
     *
     * @return int|null
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }
    
    /**
     * Get error message if request failed
     *
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Get reset token if request succeeded
     *
     * @return string|null
     */
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }
}
