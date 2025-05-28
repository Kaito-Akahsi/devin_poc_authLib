<?php

namespace AuthLib\Auth;

/**
 * Constants for error codes used in the authentication library
 */
class ErrorCode
{
    /**
     * No error
     */
    public const NONE = 0;
    
    /**
     * General authentication error
     */
    public const AUTH_GENERAL_ERROR = 400;
    
    /**
     * Authentication failed (invalid credentials)
     */
    public const AUTH_FAILED = 401;
    
    /**
     * User not found
     */
    public const USER_NOT_FOUND = 404;
    
    /**
     * Input validation error
     */
    public const VALIDATION_ERROR = 422;
    
    /**
     * Required field missing
     */
    public const VALIDATION_REQUIRED_FIELD_MISSING = 4221;
    
    /**
     * Invalid input format
     */
    public const VALIDATION_INVALID_FORMAT = 4222;
    
    /**
     * Database error
     */
    public const DATABASE_ERROR = 500;
    
    /**
     * Database connection error
     */
    public const DATABASE_CONNECTION_ERROR = 5001;
    
    /**
     * Database query error
     */
    public const DATABASE_QUERY_ERROR = 5002;
    
    /**
     * Configuration error
     */
    public const CONFIG_ERROR = 600;
    
    /**
     * Invalid configuration
     */
    public const CONFIG_INVALID = 6001;
    
    /**
     * Missing configuration
     */
    public const CONFIG_MISSING = 6002;
    
    /**
     * Password reset error
     */
    public const PASSWORD_RESET_ERROR = 700;
    
    /**
     * Invalid reset token
     */
    public const PASSWORD_RESET_INVALID_TOKEN = 7001;
    
    /**
     * Expired reset token
     */
    public const PASSWORD_RESET_EXPIRED_TOKEN = 7002;
    
    /**
     * SSO error
     */
    public const SSO_ERROR = 800;
    
    /**
     * SSO provider error
     */
    public const SSO_PROVIDER_ERROR = 8001;
    
    /**
     * SSO callback error
     */
    public const SSO_CALLBACK_ERROR = 8002;
    
    /**
     * Get error message for an error code
     *
     * @param int $errorCode Error code
     * @return string Error message
     */
    public static function getMessage(int $errorCode): string
    {
        return match($errorCode) {
            self::NONE => 'No error',
            self::AUTH_GENERAL_ERROR => 'Authentication error',
            self::AUTH_FAILED => 'Authentication failed',
            self::USER_NOT_FOUND => 'User not found',
            self::VALIDATION_ERROR => 'Validation error',
            self::VALIDATION_REQUIRED_FIELD_MISSING => 'Required field missing',
            self::VALIDATION_INVALID_FORMAT => 'Invalid input format',
            self::DATABASE_ERROR => 'Database error',
            self::DATABASE_CONNECTION_ERROR => 'Database connection error',
            self::DATABASE_QUERY_ERROR => 'Database query error',
            self::CONFIG_ERROR => 'Configuration error',
            self::CONFIG_INVALID => 'Invalid configuration',
            self::CONFIG_MISSING => 'Missing configuration',
            self::PASSWORD_RESET_ERROR => 'Password reset error',
            self::PASSWORD_RESET_INVALID_TOKEN => 'Invalid reset token',
            self::PASSWORD_RESET_EXPIRED_TOKEN => 'Expired reset token',
            self::SSO_ERROR => 'SSO error',
            self::SSO_PROVIDER_ERROR => 'SSO provider error',
            self::SSO_CALLBACK_ERROR => 'SSO callback error',
            default => 'Unknown error'
        };
    }
}
