# Extending the PHP Authentication Library

This guide provides detailed information on how to extend the PHP Authentication Library for custom functionality.

## Table of Contents

1. [Extension Points](#extension-points)
2. [Creating Custom Data Stores](#creating-custom-data-stores)
3. [Implementing SSO Providers](#implementing-sso-providers)
4. [Custom Password Reset Flows](#custom-password-reset-flows)
5. [Adding New Error Codes](#adding-new-error-codes)
6. [Custom Validation Rules](#custom-validation-rules)

## Extension Points

The library is designed with several extension points:

- **Data Store Interface**: Create custom data stores for different storage backends
- **Authentication Interface**: Implement custom authentication logic
- **SSO Interface**: Add support for various SSO providers
- **Validation Interface**: Create custom validation rules
- **Error Codes**: Extend error codes for more detailed error reporting

## Creating Custom Data Stores

To create a custom data store, implement the `DataStoreInterface`:

```php
<?php

namespace YourNamespace\DataStore;

use AuthLib\DataStore\DataStoreInterface;
use AuthLib\DataStore\UserCredentials;

class RedisDataStore implements DataStoreInterface
{
    private $redis;
    
    public function __construct(array $config)
    {
        $this->redis = new \Redis();
        $this->redis->connect(
            $config['authlib.datastore.redis.host'] ?? 'localhost',
            $config['authlib.datastore.redis.port'] ?? 6379
        );
        
        if (isset($config['authlib.datastore.redis.password'])) {
            $this->redis->auth($config['authlib.datastore.redis.password']);
        }
    }
    
    public function getUserCredentials(string $userId): ?UserCredentials
    {
        $data = $this->redis->hGetAll("user:{$userId}");
        
        if (empty($data)) {
            return null;
        }
        
        return new UserCredentials(
            $userId,
            $data['hashed_password'],
            $data['salt']
        );
    }
    
    public function storePasswordResetToken(string $userId, string $resetToken, int $expiresAt): bool
    {
        // Check if user exists
        if (!$this->redis->exists("user:{$userId}")) {
            return false;
        }
        
        // Store token with expiration
        $this->redis->hMSet("reset_token:{$userId}", [
            'token' => $resetToken,
            'expires_at' => $expiresAt
        ]);
        
        // Set expiration on the key itself
        $ttl = $expiresAt - time();
        if ($ttl > 0) {
            $this->redis->expire("reset_token:{$userId}", $ttl);
        }
        
        return true;
    }
    
    // Implement other interface methods...
}
```

Then register your custom data store with the factory:

```php
<?php

use AuthLib\DataStore\DataStoreFactory;
use YourNamespace\DataStore\RedisDataStore;

// Register custom data store
DataStoreFactory::registerDataStore('redis', RedisDataStore::class);

// Use custom data store
$config = [
    'authlib.datastore.type' => 'redis',
    'authlib.datastore.redis.host' => 'redis.example.com',
    'authlib.datastore.redis.port' => '6379'
];

$dataStore = DataStoreFactory::create($config);
```

## Implementing SSO Providers

To implement an SSO provider, create a class that implements the `SsoInterface`:

```php
<?php

namespace YourNamespace\Auth;

use AuthLib\Auth\AuthResult;
use AuthLib\Auth\ErrorCode;
use AuthLib\Auth\SsoInterface;

class GoogleSsoProvider implements SsoInterface
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    
    public function __construct(string $clientId, string $clientSecret, string $redirectUri)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }
    
    public function initiateSsoAuth(string $provider, array $options = []): string
    {
        if ($provider !== 'google') {
            throw new \InvalidArgumentException("Unsupported provider: {$provider}");
        }
        
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'email profile',
            'state' => bin2hex(random_bytes(16)) // CSRF protection
        ];
        
        // Store state in session for verification
        $_SESSION['sso_state'] = $params['state'];
        
        return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
    }
    
    public function processSsoCallback(string $provider, array $callbackData): AuthResult
    {
        if ($provider !== 'google') {
            return new AuthResult(false, ErrorCode::SSO_PROVIDER_ERROR);
        }
        
        // Verify state to prevent CSRF
        if (!isset($callbackData['state']) || 
            !isset($_SESSION['sso_state']) || 
            $callbackData['state'] !== $_SESSION['sso_state']) {
            return new AuthResult(false, ErrorCode::SSO_CALLBACK_ERROR);
        }
        
        // Clear state
        unset($_SESSION['sso_state']);
        
        // Check for error
        if (isset($callbackData['error'])) {
            return new AuthResult(false, ErrorCode::SSO_CALLBACK_ERROR);
        }
        
        // Check for authorization code
        if (!isset($callbackData['code'])) {
            return new AuthResult(false, ErrorCode::SSO_CALLBACK_ERROR);
        }
        
        // Exchange code for token (implementation details omitted)
        // ...
        
        // Get user info from Google API (implementation details omitted)
        // ...
        
        // Return success with user info
        return new AuthResult(true);
    }
    
    // Implement other interface methods...
}
```

## Custom Password Reset Flows

To implement a custom password reset flow:

```php
<?php

namespace YourNamespace\Auth;

use AuthLib\Auth\AuthInterface;
use AuthLib\Auth\AuthResult;
use AuthLib\Auth\ErrorCode;
use AuthLib\DataStore\DataStoreInterface;

class CustomAuthService implements AuthInterface
{
    private $dataStore;
    private $emailService;
    
    public function __construct(DataStoreInterface $dataStore, EmailService $emailService)
    {
        $this->dataStore = $dataStore;
        $this->emailService = $emailService;
    }
    
    public function requestPasswordReset(string $userId): AuthResult
    {
        $userCredentials = $this->dataStore->getUserCredentials($userId);
        
        if ($userCredentials === null) {
            return new AuthResult(false, ErrorCode::USER_NOT_FOUND);
        }
        
        // Generate reset token
        $resetToken = bin2hex(random_bytes(32));
        
        // Set expiration time (24 hours from now)
        $expiresAt = time() + 86400;
        
        // Store token
        $this->dataStore->storePasswordResetToken($userId, $resetToken, $expiresAt);
        
        // Send email with reset link
        $resetLink = "https://example.com/reset-password?user={$userId}&token={$resetToken}";
        $this->emailService->sendPasswordResetEmail($userId, $resetLink);
        
        return new AuthResult(true);
    }
    
    public function resetPassword(string $userId, string $resetToken, string $newPassword): AuthResult
    {
        // Verify token
        if (!$this->dataStore->verifyPasswordResetToken($userId, $resetToken)) {
            return new AuthResult(false, ErrorCode::PASSWORD_RESET_INVALID_TOKEN);
        }
        
        $userCredentials = $this->dataStore->getUserCredentials($userId);
        
        if ($userCredentials === null) {
            return new AuthResult(false, ErrorCode::USER_NOT_FOUND);
        }
        
        // Generate new salt
        $salt = bin2hex(random_bytes(16));
        
        // Hash new password
        $hashedPassword = hash('sha256', $newPassword . $salt);
        
        // Update user credentials
        $this->dataStore->updateUser($userId, $hashedPassword, $salt);
        
        // Clear reset token
        $this->dataStore->clearPasswordResetToken($userId);
        
        return new AuthResult(true);
    }
    
    // Implement other interface methods...
}
```

## Adding New Error Codes

To add new error codes, extend the `ErrorCode` class:

```php
<?php

namespace YourNamespace\Auth;

use AuthLib\Auth\ErrorCode as BaseErrorCode;

class CustomErrorCode extends BaseErrorCode
{
    /**
     * Custom error code for account lockout
     */
    public const ACCOUNT_LOCKED = 450;
    
    /**
     * Custom error code for too many failed attempts
     */
    public const TOO_MANY_ATTEMPTS = 451;
    
    /**
     * {@inheritdoc}
     */
    public static function getMessage(int $errorCode): string
    {
        return match($errorCode) {
            self::ACCOUNT_LOCKED => 'Account is locked',
            self::TOO_MANY_ATTEMPTS => 'Too many failed login attempts',
            default => parent::getMessage($errorCode)
        };
    }
}
```

## Custom Validation Rules

To implement custom validation rules:

```php
<?php

namespace YourNamespace\Validation;

use AuthLib\Validation\InputValidatorInterface;

class EnhancedValidator implements InputValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function validateRequired(array $input, array $requiredFields): bool
    {
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || $input[$field] === '') {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validate email format
     *
     * @param string $email Email to validate
     * @return bool Whether the email is valid
     */
    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate password strength
     *
     * @param string $password Password to validate
     * @return bool Whether the password is strong enough
     */
    public function validatePasswordStrength(string $password): bool
    {
        // At least 8 characters
        if (strlen($password) < 8) {
            return false;
        }
        
        // At least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        // At least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
        
        // At least one number
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        // At least one special character
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
}
```
