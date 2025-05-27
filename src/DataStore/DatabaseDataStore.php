<?php

namespace AuthLib\DataStore;

<<<<<<< HEAD
use PDO;
use PDOException;
use AuthLib\Auth\ErrorCode;
use AuthLib\Config\ConfigReader;

||||||| af0d3de
=======
use PDO;
use PDOException;
use AuthLib\Config\ConfigReader;

>>>>>>> origin/main
/**
 * Implementation of data store using database storage
 */
class DatabaseDataStore implements DataStoreInterface
{
    /**
     * @var PDO|null Database connection
     */
    private ?PDO $connection = null;
    
    /**
     * @var array Configuration options
     */
    private array $config;
<<<<<<< HEAD
    
    /**
     * @var string Database driver (mysql, pgsql)
     */
    private string $driver;
    
    /**
     * @var bool Whether to use connection pooling
     */
    private bool $useConnectionPool = false;
    
    /**
     * @var int Maximum number of connections in the pool
     */
    private int $maxConnections = 5;
||||||| af0d3de
=======
    
    /**
     * @var string Database driver (mysql, pgsql)
     */
    private string $driver;
>>>>>>> origin/main

    /**
     * DatabaseDataStore constructor
     *
     * @param array $config Configuration options
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
<<<<<<< HEAD
        $this->driver = $config['authlib.datastore.database.driver'] ?? 'mysql';
        
        if (isset($config['authlib.datastore.database.use_connection_pool']) && 
            $config['authlib.datastore.database.use_connection_pool'] === '1') {
            $this->useConnectionPool = true;
            
            if (isset($config['authlib.datastore.database.max_connections'])) {
                $this->maxConnections = (int)$config['authlib.datastore.database.max_connections'];
            }
        }
        
        if (isset($config['authlib.datastore.database.auto_connect']) && 
            $config['authlib.datastore.database.auto_connect'] === '1') {
            $this->connect();
        }
||||||| af0d3de
=======
        $this->driver = $config['authlib.datastore.database.driver'] ?? 'mysql';
        
        if (isset($config['authlib.datastore.database.auto_connect']) && 
            $config['authlib.datastore.database.auto_connect'] === '1') {
            $this->connect();
        }
>>>>>>> origin/main
    }
<<<<<<< HEAD
    
    /**
     * Connect to the database
     *
     * @return bool Whether connection was successful
     * @throws PDOException If connection fails
     */
    public function connect(): bool
    {
        if ($this->connection !== null) {
            return true;
        }
        
        $host = $this->config['authlib.datastore.database.host'] ?? 'localhost';
        $port = $this->config['authlib.datastore.database.port'] ?? '3306';
        $dbname = $this->config['authlib.datastore.database.dbname'] ?? 'authlib';
        $username = $this->config['authlib.datastore.database.username'] ?? 'root';
        $password = $this->config['authlib.datastore.database.password'] ?? '';
        $charset = $this->config['authlib.datastore.database.charset'] ?? 'utf8mb4';
        
        $dsn = "{$this->driver}:host={$host};port={$port};dbname={$dbname};charset={$charset}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        if ($this->useConnectionPool) {
            $options[PDO::ATTR_PERSISTENT] = true;
        }
        
        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
||||||| af0d3de

=======
    
    /**
     * Connect to the database
     *
     * @return bool Whether connection was successful
     * @throws PDOException If connection fails
     */
    public function connect(): bool
    {
        if ($this->connection !== null) {
            return true;
        }
        
        $host = $this->config['authlib.datastore.database.host'] ?? 'localhost';
        $port = $this->config['authlib.datastore.database.port'] ?? '3306';
        $dbname = $this->config['authlib.datastore.database.dbname'] ?? 'authlib';
        $username = $this->config['authlib.datastore.database.username'] ?? 'root';
        $password = $this->config['authlib.datastore.database.password'] ?? '';
        $charset = $this->config['authlib.datastore.database.charset'] ?? 'utf8mb4';
        
        $dsn = "{$this->driver}:host={$host};port={$port};dbname={$dbname};charset={$charset}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
>>>>>>> origin/main
    /**
     * {@inheritdoc}
     */
    public function getUserCredentials(string $userId): ?UserCredentials
    {
<<<<<<< HEAD
        if ($this->connection === null) {
            $this->connect();
        }
        
        $stmt = $this->connection->prepare(
            "SELECT user_id, hashed_password, salt, created_at, updated_at 
             FROM user_credentials 
             WHERE user_id = :user_id"
        );
        
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        
        if ($result === false) {
            return null;
        }
        
        return new UserCredentials(
            $result['user_id'],
            $result['hashed_password'],
            $result['salt']
        );
    }
    
    /**
     * Add a new user to the database
     *
     * @param string $userId User ID
     * @param string $hashedPassword Hashed password
     * @param string $salt Salt used for hashing
     * @return bool Whether the operation was successful
     */
    public function addUser(string $userId, string $hashedPassword, string $salt): bool
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        $stmt = $this->connection->prepare(
            "INSERT INTO user_credentials (user_id, hashed_password, salt, created_at, updated_at)
             VALUES (:user_id, :hashed_password, :salt, NOW(), NOW())"
        );
        
        return $stmt->execute([
            'user_id' => $userId,
            'hashed_password' => $hashedPassword,
            'salt' => $salt
        ]);
    }
    
    /**
     * Update user credentials in the database
     *
     * @param string $userId User ID
     * @param string $hashedPassword Hashed password
     * @param string $salt Salt used for hashing
     * @return bool Whether the operation was successful
     */
    public function updateUser(string $userId, string $hashedPassword, string $salt): bool
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        $stmt = $this->connection->prepare(
            "UPDATE user_credentials 
             SET hashed_password = :hashed_password, 
                 salt = :salt, 
                 updated_at = NOW() 
             WHERE user_id = :user_id"
        );
        
        return $stmt->execute([
            'user_id' => $userId,
            'hashed_password' => $hashedPassword,
            'salt' => $salt
        ]);
    }
    
    /**
     * Delete a user from the database
     *
     * @param string $userId User ID
     * @return bool Whether the operation was successful
     */
    public function deleteUser(string $userId): bool
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        $stmt = $this->connection->prepare(
            "DELETE FROM user_credentials WHERE user_id = :user_id"
        );
        
        return $stmt->execute(['user_id' => $userId]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function storePasswordResetToken(string $userId, string $resetToken, int $expiresAt): bool
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        $this->ensureResetTokensTableExists();
        
        if (!$this->userExists($userId)) {
            return false;
        }
        
        $this->clearPasswordResetToken($userId);
        
        $stmt = $this->connection->prepare(
            "INSERT INTO reset_tokens (user_id, token, expires_at, created_at)
             VALUES (:user_id, :token, FROM_UNIXTIME(:expires_at), NOW())"
        );
        
        return $stmt->execute([
            'user_id' => $userId,
            'token' => $resetToken,
            'expires_at' => $expiresAt
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function verifyPasswordResetToken(string $userId, string $resetToken): bool
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        $this->ensureResetTokensTableExists();
        
        $stmt = $this->connection->prepare(
            "SELECT token, expires_at 
             FROM reset_tokens 
             WHERE user_id = :user_id 
             AND token = :token 
             AND expires_at > NOW()"
        );
        
        $stmt->execute([
            'user_id' => $userId,
            'token' => $resetToken
        ]);
        
        return $stmt->fetch() !== false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function clearPasswordResetToken(string $userId): bool
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        $this->ensureResetTokensTableExists();
        
        $stmt = $this->connection->prepare(
            "DELETE FROM reset_tokens WHERE user_id = :user_id"
        );
        
        return $stmt->execute(['user_id' => $userId]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function storeUserMetadata(string $userId, string $key, $value): bool
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        $this->ensureUserMetadataTableExists();
        
        if (!$this->userExists($userId)) {
            return false;
        }
        
        if (!is_scalar($value)) {
            $value = serialize($value);
        }
        
        $stmt = $this->connection->prepare(
            "SELECT user_id FROM user_metadata 
             WHERE user_id = :user_id AND meta_key = :meta_key"
        );
        
        $stmt->execute([
            'user_id' => $userId,
            'meta_key' => $key
        ]);
        
        if ($stmt->fetch() !== false) {
            $stmt = $this->connection->prepare(
                "UPDATE user_metadata 
                 SET meta_value = :meta_value, updated_at = NOW() 
                 WHERE user_id = :user_id AND meta_key = :meta_key"
            );
        } else {
            $stmt = $this->connection->prepare(
                "INSERT INTO user_metadata (user_id, meta_key, meta_value, created_at, updated_at)
                 VALUES (:user_id, :meta_key, :meta_value, NOW(), NOW())"
            );
        }
        
        return $stmt->execute([
            'user_id' => $userId,
            'meta_key' => $key,
            'meta_value' => $value
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUserMetadata(string $userId, string $key)
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        $this->ensureUserMetadataTableExists();
        
        $stmt = $this->connection->prepare(
            "SELECT meta_value FROM user_metadata 
             WHERE user_id = :user_id AND meta_key = :meta_key"
        );
        
        $stmt->execute([
            'user_id' => $userId,
            'meta_key' => $key
        ]);
        
        $result = $stmt->fetch();
        
        if ($result === false) {
            return null;
        }
        
        $value = $result['meta_value'];
        
        if (is_string($value) && preg_match('/^[aObis]:\d+:/', $value)) {
            $unserialized = @unserialize($value);
            if ($unserialized !== false) {
                return $unserialized;
            }
        }
        
        return $value;
    }
    
    /**
     * Check if a user exists in the database
     *
     * @param string $userId User ID
     * @return bool Whether the user exists
     */
    private function userExists(string $userId): bool
    {
        $stmt = $this->connection->prepare(
            "SELECT 1 FROM user_credentials WHERE user_id = :user_id"
        );
        
        $stmt->execute(['user_id' => $userId]);
        
        return $stmt->fetch() !== false;
    }
    
    /**
     * Ensure the reset_tokens table exists
     *
     * @return void
     */
    private function ensureResetTokensTableExists(): void
    {
        $tableExists = false;
        
        if ($this->driver === 'mysql') {
            $stmt = $this->connection->prepare(
                "SELECT 1 FROM information_schema.tables 
                 WHERE table_schema = DATABASE() AND table_name = 'reset_tokens'"
            );
            $stmt->execute();
            $tableExists = $stmt->fetch() !== false;
        } elseif ($this->driver === 'pgsql') {
            $stmt = $this->connection->prepare(
                "SELECT 1 FROM information_schema.tables 
                 WHERE table_schema = 'public' AND table_name = 'reset_tokens'"
            );
            $stmt->execute();
            $tableExists = $stmt->fetch() !== false;
        }
        
        if (!$tableExists) {
            if ($this->driver === 'mysql') {
                $this->connection->exec(
                    "CREATE TABLE reset_tokens (
                        user_id VARCHAR(255) NOT NULL,
                        token VARCHAR(255) NOT NULL,
                        expires_at TIMESTAMP NOT NULL,
                        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (user_id),
                        INDEX idx_token (token),
                        INDEX idx_expires_at (expires_at),
                        CONSTRAINT fk_reset_tokens_user_id FOREIGN KEY (user_id)
                            REFERENCES user_credentials (user_id) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
                );
            } elseif ($this->driver === 'pgsql') {
                $this->connection->exec(
                    "CREATE TABLE reset_tokens (
                        user_id VARCHAR(255) NOT NULL,
                        token VARCHAR(255) NOT NULL,
                        expires_at TIMESTAMP NOT NULL,
                        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (user_id),
                        CONSTRAINT fk_reset_tokens_user_id FOREIGN KEY (user_id)
                            REFERENCES user_credentials (user_id) ON DELETE CASCADE
                    )"
                );
                
                $this->connection->exec("CREATE INDEX idx_token ON reset_tokens (token)");
                $this->connection->exec("CREATE INDEX idx_expires_at ON reset_tokens (expires_at)");
            }
        }
    }
    
    /**
     * Ensure the user_metadata table exists
     *
     * @return void
     */
    private function ensureUserMetadataTableExists(): void
    {
        $tableExists = false;
        
        if ($this->driver === 'mysql') {
            $stmt = $this->connection->prepare(
                "SELECT 1 FROM information_schema.tables 
                 WHERE table_schema = DATABASE() AND table_name = 'user_metadata'"
            );
            $stmt->execute();
            $tableExists = $stmt->fetch() !== false;
        } elseif ($this->driver === 'pgsql') {
            $stmt = $this->connection->prepare(
                "SELECT 1 FROM information_schema.tables 
                 WHERE table_schema = 'public' AND table_name = 'user_metadata'"
            );
            $stmt->execute();
            $tableExists = $stmt->fetch() !== false;
        }
        
        if (!$tableExists) {
            if ($this->driver === 'mysql') {
                $this->connection->exec(
                    "CREATE TABLE user_metadata (
                        user_id VARCHAR(255) NOT NULL,
                        meta_key VARCHAR(255) NOT NULL,
                        meta_value TEXT,
                        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (user_id, meta_key),
                        INDEX idx_user_id (user_id),
                        CONSTRAINT fk_user_metadata_user_id FOREIGN KEY (user_id)
                            REFERENCES user_credentials (user_id) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
                );
            } elseif ($this->driver === 'pgsql') {
                $this->connection->exec(
                    "CREATE TABLE user_metadata (
                        user_id VARCHAR(255) NOT NULL,
                        meta_key VARCHAR(255) NOT NULL,
                        meta_value TEXT,
                        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (user_id, meta_key),
                        CONSTRAINT fk_user_metadata_user_id FOREIGN KEY (user_id)
                            REFERENCES user_credentials (user_id) ON DELETE CASCADE
                    )"
                );
                
                $this->connection->exec("CREATE INDEX idx_user_metadata_user_id ON user_metadata (user_id)");
                
                $this->connection->exec(
                    "CREATE TRIGGER update_user_metadata_updated_at
                     BEFORE UPDATE ON user_metadata
                     FOR EACH ROW
                     EXECUTE FUNCTION update_updated_at_column()"
                );
            }
        }
    }
    
    /**
     * Close the database connection
     *
     * @return void
     */
    public function close(): void
    {
        $this->connection = null;
||||||| af0d3de
        return null;
=======
        if ($this->connection === null) {
            $this->connect();
        }
        
        $stmt = $this->connection->prepare(
            "SELECT user_id, hashed_password, salt, created_at, updated_at 
             FROM user_credentials 
             WHERE user_id = :user_id"
        );
        
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        
        if ($result === false) {
            return null;
        }
        
        return new UserCredentials(
            $result['user_id'],
            $result['hashed_password'],
            $result['salt']
        );
    }
    
    /**
     * Add a new user to the database
     *
     * @param string $userId User ID
     * @param string $hashedPassword Hashed password
     * @param string $salt Salt used for hashing
     * @return bool Whether the operation was successful
     */
    public function addUser(string $userId, string $hashedPassword, string $salt): bool
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        $stmt = $this->connection->prepare(
            "INSERT INTO user_credentials (user_id, hashed_password, salt, created_at, updated_at)
             VALUES (:user_id, :hashed_password, :salt, NOW(), NOW())"
        );
        
        return $stmt->execute([
            'user_id' => $userId,
            'hashed_password' => $hashedPassword,
            'salt' => $salt
        ]);
    }
    
    /**
     * Update user credentials in the database
     *
     * @param string $userId User ID
     * @param string $hashedPassword Hashed password
     * @param string $salt Salt used for hashing
     * @return bool Whether the operation was successful
     */
    public function updateUser(string $userId, string $hashedPassword, string $salt): bool
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        $stmt = $this->connection->prepare(
            "UPDATE user_credentials 
             SET hashed_password = :hashed_password, 
                 salt = :salt, 
                 updated_at = NOW() 
             WHERE user_id = :user_id"
        );
        
        return $stmt->execute([
            'user_id' => $userId,
            'hashed_password' => $hashedPassword,
            'salt' => $salt
        ]);
    }
    
    /**
     * Delete a user from the database
     *
     * @param string $userId User ID
     * @return bool Whether the operation was successful
     */
    public function deleteUser(string $userId): bool
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        $stmt = $this->connection->prepare(
            "DELETE FROM user_credentials WHERE user_id = :user_id"
        );
        
        return $stmt->execute(['user_id' => $userId]);
    }
    
    /**
     * Close the database connection
     *
     * @return void
     */
    public function close(): void
    {
        $this->connection = null;
>>>>>>> origin/main
    }
}
