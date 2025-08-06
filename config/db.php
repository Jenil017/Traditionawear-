<?php
/**
 * Database Configuration for RTWRS
 * Rameshwar Traditional Wear Rental System
 */

// Database configuration
$host = 'localhost';
$db   = 'rtwrs';  // Fixed database name to match expected
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// PDO connection string
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options for security and error handling
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create PDO connection
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Test the connection
    $pdo->query('SELECT 1');
    
} catch (PDOException $e) {
    // Log the error and show user-friendly message
    error_log('Database Connection Error: ' . $e->getMessage());
    
    // Show different messages based on environment
    if (defined('DEVELOPMENT') && DEVELOPMENT === true) {
        die('Database Connection Error: ' . $e->getMessage());
    } else {
        die('Database connection failed. Please try again later.');
    }
}

// Set timezone for database operations
try {
    $pdo->exec("SET time_zone = '+00:00'");
} catch (PDOException $e) {
    error_log('Timezone setting failed: ' . $e->getMessage());
}
?>