<?php
header("Content-Type: application/json");
require_once "db.php";

try {

    $stmt = $pdo->query("
        SELECT report_date
        FROM google_ads_data
        ORDER BY report_date ASC
    ");

    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        "success" => true,
        "dates" => $dates
    ]);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "error" => "Failed to load dates",
        "message" => $e->getMessage()
    ]);
}
