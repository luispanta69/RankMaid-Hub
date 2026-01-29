<?php
header("Content-Type: application/json");
require_once "db.php";
session_start();

/**
 * 1. Find latest available date
 */
$latestStmt = $pdo->query("
  SELECT MAX(report_date) AS latest_date
  FROM facebook_ads_data
");
$latest = $latestStmt->fetch(PDO::FETCH_ASSOC)["latest_date"];

if (!$latest) {
    echo json_encode(["headers" => null, "rows" => []]);
    exit;
}

/**
 * 2. Compute month range
 */
$start = date("Y-m-01", strtotime($latest));
$end = date("Y-m-t", strtotime($latest));

/**
 * 3. Load rows for that month
 */
$stmt = $pdo->prepare("
  SELECT raw_row
  FROM facebook_ads_data
  WHERE report_date BETWEEN ? AND ?
  ORDER BY report_date ASC
");
$stmt->execute([$start, $end]);

$rows = [];
$headers = $_SESSION["facebook_csv_headers"] ?? null;

while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dayRows = json_decode($r["raw_row"], true);
    foreach ($dayRows as $row) {
        $rows[] = $row;
    }
}

echo json_encode([
    "headers" => $headers,
    "rows" => $rows,
    "month" => date("F Y", strtotime($latest)),
    "start" => $start,
    "end" => $end
]);
