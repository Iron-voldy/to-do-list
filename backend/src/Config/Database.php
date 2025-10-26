<?php

namespace App\Config;

use PDO;
use PDOException;

/**
 * Database connection class following Singleton pattern
 */
class Database
{
    private static ?PDO $connection = null;

    /**
     * Get database connection
     *
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host = getenv('DB_HOST') ?: 'db';
            $port = getenv('DB_PORT') ?: '3306';
            $dbname = getenv('DB_NAME') ?: 'todo_app';
            $username = getenv('DB_USER') ?: 'todo_user';
            $password = getenv('DB_PASSWORD') ?: 'todo_password';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$connection = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw $e;
            }
        }

        return self::$connection;
    }

    /**
     * Close database connection
     */
    public static function closeConnection(): void
    {
        self::$connection = null;
    }

    /**
     * Set database connection (mainly for testing)
     */
    public static function setConnection(?PDO $connection): void
    {
        self::$connection = $connection;
    }
}
