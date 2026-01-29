<?php
/**
 * Database Connection File
 * Uses PDO for secure, consistent database access.
 */

$host = "localhost";
$db   = "grumpyhare__wp_dfy25";
$user = "root";
$pass = ""; // Default XAMPP password is empty

// Set PDO options for better error handling and security
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Return arrays by default
    PDO::ATTR_EMULATE_PREPARES   => false,                 // Use real prepared statements
];

try {
    // Create a new PDO instance
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        $options
    );

    // Enforce strict SQL mode for data integrity
    $pdo->exec("SET sql_mode = 'STRICT_ALL_TABLES'");
    
    // Set time zone to UTC to ensure consistency across servers
    $pdo->exec("SET time_zone = '+00:00'");

} catch (PDOException $e) {
    // If connection fails, return a 500 error and stop execution
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed: " . $e->getMessage()
    ]);
    exit;
}
?>