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
     * AuthResult constructor
     *
     * @param bool $isSucceeded Whether authentication succeeded
     * @param int|null $errorCode Error code if authentication failed
     */
    public function __construct(bool $isSucceeded, ?int $errorCode = null)
    {
        $this->isSucceeded = $isSucceeded;
        $this->errorCode = $errorCode;
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
}
