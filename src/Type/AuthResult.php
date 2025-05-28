<?php

namespace AuthLib\Auth;

/**
 * Class representing the result of an authentication attempt
 */
class AuthResult
{
    /**
     * @var bool Whether the authentication was successful
     */
    private bool $isSucceeded;

    /**
     * @var int|null Error code if authentication failed
     */
    private ?int $errorCode;
    
    /**
     * @var string|null Error message if authentication failed
     */
    private ?string $errorMessage;

    /**
     * AuthResult constructor
     *
     * @param bool $isSucceeded Whether authentication succeeded
     * @param int|null $errorCode Error code if authentication failed
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
     * Get whether authentication succeeded
     *
     * @return bool
     */
    public function isSucceeded(): bool
    {
        return $this->isSucceeded;
    }

    /**
     * Get error code if authentication failed
     *
     * @return int|null
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }
    
    /**
     * Get error message if authentication failed
     *
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
