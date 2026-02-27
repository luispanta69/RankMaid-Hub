<?php
header("Content-Type: application/json");
require_once "db.php";

$start = $_GET["start"] ?? null;
$end = $_GET["end"] ?? null;

if (!$start || !$end) {
    http_response_code(400);
    echo json_encode(["error" => "Missing date range"]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT headers, raw_row
    FROM facebook_ads_data
    WHERE report_date BETWEEN ? AND ?
    ORDER BY report_date ASC
");

$stmt->execute([$start, $end]);

$rows = [];
$headers = [];

while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

    if (empty($headers) && !empty($r["headers"])) {
        $headers = json_decode($r["headers"], true);
    }

    $dayRows = json_decode($r["raw_row"], true);

    if (is_array($dayRows)) {
        foreach ($dayRows as $row) {
            $rows[] = $row;
        }
    }
}

echo json_encode([
    "headers" => $headers,
    "rows" => $rows
]);
