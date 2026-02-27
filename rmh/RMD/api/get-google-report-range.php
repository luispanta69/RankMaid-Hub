<?php
header("Content-Type: application/json");
require_once "db.php";
session_start();

$start = $_GET["start"] ?? null;
$end = $_GET["end"] ?? null;

if (!$start || !$end) {
    http_response_code(400);
    echo json_encode(["error" => "Missing start or end date"]);
    exit;
}

try {

    $stmt = $pdo->prepare("
        SELECT raw_row
        FROM google_ads_data
        WHERE report_date BETWEEN :start AND :end
        ORDER BY report_date ASC
    ");

    $stmt->execute([
        ":start" => $start,
        ":end" => $end
    ]);

    $rows = [];

    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dayRows = json_decode($r["raw_row"], true);

        if (is_array($dayRows)) {
            $rows = array_merge($rows, $dayRows);
        }
    }

    echo json_encode([
        "success" => true,
        "headers" => $_SESSION["google_csv_headers"] ?? [],
        "rows" => $rows
    ]);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "error" => "Failed to load Google report range",
        "message" => $e->getMessage()
    ]);
}
