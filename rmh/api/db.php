<?php
/**
 * PostgreSQL Database Connection (Neon)
 * Complete replacement for MySQL db.php
 */

// Neon connection details
$dsn = "pgsql:host=ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech;port=5432;dbname=neondb;sslmode=require;options=endpoint=ep-restless-bird-ahug88k0";
$user = "neondb_owner";
$pass = "npg_kvbAhwHVu15g";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// This variable will be available in the scripts that include this file.
$pdo = null;
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET TIME ZONE 'UTC'");
} catch (PDOException $e) {
    // Let the script that includes this file decide how to handle connection errors.
    // For now, $pdo will remain null and isset($pdo) will be false.
}
