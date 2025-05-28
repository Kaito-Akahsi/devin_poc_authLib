# PHP Authentication Library - Usage Guide

## Table of Contents

1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Basic Usage](#basic-usage)
4. [Configuration](#configuration)
5. [Data Store Options](#data-store-options)
6. [Error Handling](#error-handling)
7. [Password Reset](#password-reset)
8. [SSO Integration](#sso-integration)
9. [Extending the Library](#extending-the-library)
10. [Best Practices](#best-practices)

## Introduction

The PHP Authentication Library provides a flexible, secure authentication solution that can be used across multiple systems. It handles user authentication with configurable data stores, secure password hashing, and extensible interfaces for future enhancements.

## Installation

Install the library using Composer:

```bash
composer require authlib/auth-library
```

## Basic Usage

### Setting Up Authentication

```php
<?php

use AuthLib\Auth\AuthService;
use AuthLib\DataStore\DataStoreFactory;
use AuthLib\Validation\InputValidator;
use AuthLib\Auth\PasswordHasher;
use AuthLib\Auth\SaltGenerator;
use AuthLib\Config\ConfigReader;

// Create dependencies
$dataStore = DataStoreFactory::create(ConfigReader::getConfigByPrefix('authlib.datastore.'));
$validator = new InputValidator();
$saltGenerator = new SaltGenerator();
$passwordHasher = new PasswordHasher($saltGenerator);

// Create auth service
$authService = new AuthService($dataStore, $validator, $passwordHasher);

// Use the auth service
$result = $authService->login('username', 'password');

if ($result->isSucceeded()) {
    // Authentication successful
    echo "Login successful!";
} else {
    // Authentication failed
    echo "Login failed: " . $result->getErrorMessage();
    echo "Error code: " . $result->getErrorCode();
}
```

### Creating a New User

```php
<?php

// Assuming $dataStore and $passwordHasher are already created

// Generate a salt for the new user
$salt = $saltGenerator->generateSalt();

// Hash the password with the salt
$hashedPassword = $passwordHasher->hashPassword('user_password', $salt);

// Store the user credentials
$dataStore->addUser('username', $hashedPassword, $salt);
```

## Configuration

The library can be configured using php.ini settings or by loading configuration from external files.

### PHP.ini Configuration

Add the following to your php.ini file:

```ini
; Data store type (memory or database)
authlib.datastore.type = "database"

; Database configuration
authlib.datastore.database.driver = "mysql"
authlib.datastore.database.host = "localhost"
authlib.datastore.database.port = "3306"
authlib.datastore.database.dbname = "authlib"
authlib.datastore.database.username = "user"
authlib.datastore.database.password = "password"
authlib.datastore.database.charset = "utf8mb4"
authlib.datastore.database.auto_connect = "1"

; Connection pooling (optional)
authlib.datastore.database.use_connection_pool = "1"
authlib.datastore.database.max_connections = "10"
```

### External Configuration File

You can also load configuration from an external file:

```php
<?php

use AuthLib\Config\ConfigLoader;

$configLoader = new ConfigLoader();
$config = $configLoader->loadFromFile('/path/to/config/authlib.ini');

$dataStore = DataStoreFactory::create($config);
```

## Data Store Options

The library supports multiple data store backends:

### Memory Data Store

Useful for testing or small applications:

```php
<?php

use AuthLib\DataStore\MemoryDataStore;

$config = [
    'authlib.datastore.memory.init_test_data' => '1'
];

$dataStore = new MemoryDataStore($config);
```

### Database Data Store

For production use with MySQL or PostgreSQL:

```php
<?php

use AuthLib\DataStore\DatabaseDataStore;

$config = [
    'authlib.datastore.database.driver' => 'mysql',
    'authlib.datastore.database.host' => 'localhost',
    'authlib.datastore.database.port' => '3306',
    'authlib.datastore.database.dbname' => 'authlib',
    'authlib.datastore.database.username' => 'user',
    'authlib.datastore.database.password' => 'password',
    'authlib.datastore.database.charset' => 'utf8mb4',
    'authlib.datastore.database.auto_connect' => '1'
];

$dataStore = new DatabaseDataStore($config);
```

## Error Handling

The library provides detailed error codes and messages:

```php
<?php

use AuthLib\Auth\ErrorCode;

// Get error message for a specific error code
$message = ErrorCode::getMessage(ErrorCode::AUTH_FAILED);

// Check error code from authentication result
$result = $authService->login('username', 'wrong_password');

if (!$result->isSucceeded()) {
    $errorCode = $result->getErrorCode();
    $errorMessage = $result->getErrorMessage();
    
    switch ($errorCode) {
        case ErrorCode::USER_NOT_FOUND:
            echo "User does not exist";
            break;
        case ErrorCode::AUTH_FAILED:
            echo "Invalid password";
            break;
        case ErrorCode::VALIDATION_REQUIRED_FIELD_MISSING:
            echo "Required field missing";
            break;
        default:
            echo "Unknown error: " . $errorMessage;
    }
}
```

## Password Reset

The library includes interfaces for password reset functionality:

```php
<?php

// Request a password reset
$result = $authService->requestPasswordReset('username');

if ($result->isSucceeded()) {
    // Send reset token to user (implementation depends on your application)
    // ...
}

// Reset password using token
$result = $authService->resetPassword('username', 'reset_token', 'new_password');

if ($result->isSucceeded()) {
    echo "Password reset successful";
} else {
    echo "Password reset failed: " . $result->getErrorMessage();
}
```

## SSO Integration

The library provides interfaces for SSO integration:

```php
<?php

use AuthLib\Auth\SsoInterface;

class SsoService implements SsoInterface
{
    // Implement the required methods
    // ...
}

// Usage example
$ssoService = new SsoService();

// Initiate SSO authentication
$redirectUrl = $ssoService->initiateSsoAuth('google', [
    'redirect_uri' => 'https://example.com/callback'
]);

// Process SSO callback
$result = $ssoService->processSsoCallback('google', $_GET);

if ($result->isSucceeded()) {
    echo "SSO authentication successful";
} else {
    echo "SSO authentication failed: " . $result->getErrorMessage();
}
```

## Extending the Library

The library is designed to be easily extended:

### Creating a Custom Data Store

```php
<?php

use AuthLib\DataStore\DataStoreInterface;
use AuthLib\DataStore\UserCredentials;

class RedisDataStore implements DataStoreInterface
{
    // Implement the required methods
    public function getUserCredentials(string $userId): ?UserCredentials
    {
        // Implementation
    }
    
    public function storePasswordResetToken(string $userId, string $resetToken, int $expiresAt): bool
    {
        // Implementation
    }
    
    // Implement other interface methods
    // ...
}
```

### Creating a Custom Authentication Provider

```php
<?php

use AuthLib\Auth\AuthInterface;
use AuthLib\Auth\AuthResult;

class CustomAuthService implements AuthInterface
{
    // Implement the required methods
    public function login(string $userId, string $password): AuthResult
    {
        // Implementation
    }
    
    // Implement other interface methods
    // ...
}
```

## Best Practices

1. **Always use HTTPS** when transmitting authentication data
2. **Configure proper database credentials** and limit database user permissions
3. **Implement rate limiting** to prevent brute force attacks
4. **Use prepared statements** for all database queries (already implemented in the library)
5. **Regularly rotate database credentials** and update configuration
6. **Monitor authentication failures** for suspicious activity
7. **Implement multi-factor authentication** for sensitive applications
8. **Keep the library updated** to get the latest security improvements
