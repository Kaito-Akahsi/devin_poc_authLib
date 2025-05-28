<?php

namespace AuthLib\Auth;

/**
 * Class representing the response of a password reset operation
 */
class PasswordResetResponse
{
    /**
     * @var bool Whether the password reset was successful
     */
    private bool $isSucceeded;

    /**
     * @var int|null Error code if reset failed
     */
    private ?int $errorCode;
    
    /**
     * @var string|null Error message if reset failed
     */
    private ?string $errorMessage;

    /**
     * PasswordResetResponse constructor
     *
     * @param bool $isSucceeded Whether reset succeeded
     * @param int|null $errorCode Error code if reset failed
     * @param string|null $errorMessage Custom error message (optional)
     */
    public function __construct(bool $isSucceeded, ?int $errorCode = null, ?string $errorMessage = null)
    {
        $this->isSucceeded = $isSucceeded;
        $this->errorCode = $errorCode;
        
        if ($errorMessage === null && $errorCode !== null) {
            $this->errorMessage = ErrorCode::getMessage($errorCode);
        } else {
            $this->errorMessage = $errorMessage;
        }
    }

    /**
     * Get whether reset succeeded
     *
     * @return bool
     */
    public function isSucceeded(): bool
    {
        return $this->isSucceeded;
    }

    /**
     * Get error code if reset failed
     *
     * @return int|null
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }
    
    /**
     * Get error message if reset failed
     *
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
