<?php
/**
 * Database Configuration - CBC Resource Recommender
 *
 * Defines database connection parameters and provides a singleton
 * PDO connection instance. Update credentials according to your
 * local environment (XAMPP/Laragon/etc.).
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'cbc_recommender');
define('DB_USER', 'root');
define('DB_PASS', '');          // Change this if your MySQL has a password
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a singleton PDO connection to the database.
 *
 * Uses static variable to ensure only one connection is created
 * per request. Configured with:
 * - ERRMODE_EXCEPTION for proper error handling
 * - FETCH_ASSOC for associative array results
 * - EMULATE_PREPARES disabled for true prepared statements (security)
 *
 * @return PDO The database connection object
 * @throws PDOException If the connection fails
 */
function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In production you would log this instead of showing it
            die("Database connection failed: " . $e->getMessage());
        }
    }

    return $pdo;
}
