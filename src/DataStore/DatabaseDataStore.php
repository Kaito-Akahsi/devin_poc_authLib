<?php

namespace AuthLib\DataStore;

use PDO;
use PDOException;
use AuthLib\Config\ConfigReader;

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
    
    /**
     * @var string Database driver (mysql, pgsql)
     */
    private string $driver;

    /**
     * DatabaseDataStore constructor
     *
     * @param array $config Configuration options
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->driver = $config['authlib.datastore.database.driver'] ?? 'mysql';
        
        if (isset($config['authlib.datastore.database.auto_connect']) && 
            $config['authlib.datastore.database.auto_connect'] === '1') {
            $this->connect();
        }
    }
    
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
    
    /**
     * {@inheritdoc}
     */
    public function getUserCredentials(string $userId): ?UserCredentials
    {
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
    }
}
