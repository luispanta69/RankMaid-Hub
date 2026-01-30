<?php
header("Content-Type: application/json");
require_once "db.php";
session_start();

$start = $_GET["start"] ?? null;
$end = $_GET["end"] ?? null;

if (!$start || !$end) {
    http_response_code(400);
    echo json_encode(["error" => "Missing date range"]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT raw_row
    FROM facebook_ads_data
    WHERE report_date BETWEEN ? AND ?
    ORDER BY report_date ASC
");

$stmt->execute([$start, $end]);

$rows = [];

while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // ✅ Safe JSONB handling
    $raw = $r["raw_row"];
    $dayRows = is_string($raw) ? json_decode($raw, true) : $raw;

    if (is_array($dayRows)) {
        foreach ($dayRows as $row) {
            $rows[] = $row;
        }
    }
}

echo json_encode([
    "headers" => $_SESSION["facebook_csv_headers"] ?? [],
    "rows" => $rows
]);
