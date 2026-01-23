<?php
$host = "localhost";
$db = "grumpyhare__wp_dfy25";
$user = "root";
$pass = ""; // XAMPP default

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        $options
    );

    // Safety + consistency
    $pdo->exec("SET sql_mode = 'STRICT_ALL_TABLES'");
    $pdo->exec("SET time_zone = '+00:00'");

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}