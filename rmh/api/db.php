<?php

// ✅ Neon PostgreSQL Remote Connection
$host = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech";
$db = "neondb";
$user = "neondb_owner";
$pass = "npg_kvbAhwHVu15g";
$port = "5432";

// ✅ Required SSL + Neon endpoint option
$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require;options=endpoint=ep-restless-bird-ahug88k0";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Optional: Force timezone consistency
    $pdo->exec("SET TIME ZONE 'UTC'");

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "DB connection failed",
        "details" => $e->getMessage()
    ]);
    exit;
}
