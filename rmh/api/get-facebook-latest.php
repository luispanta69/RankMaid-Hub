<?php
header("Content-Type: application/json");
require_once "db.php";

/**
 * 1. Get latest report
 */
$report = $pdo->query(
    "SELECT id, report_start, report_end
   FROM facebook_reports
   ORDER BY uploaded_at DESC
   LIMIT 1"
)->fetch();

if (!$report) {
    echo json_encode(["empty" => true]);
    exit;
}

/**
 * 2. Get headers
 */
$headersStmt = $pdo->prepare(
    "SELECT header_name
   FROM facebook_report_headers
   WHERE report_id = ?
   ORDER BY header_index ASC"
);
$headersStmt->execute([$report["id"]]);
$headers = array_column($headersStmt->fetchAll(), "header_name");

/**
 * 3. Get rows
 */
$rowsStmt = $pdo->prepare(
    "SELECT row_data
   FROM facebook_report_rows
   WHERE report_id = ?"
);
$rowsStmt->execute([$report["id"]]);

$rows = [];
foreach ($rowsStmt as $r) {
    $rows[] = json_decode($r["row_data"], true);
}

echo json_encode([
    "report" => $report,
    "headers" => $headers,
    "rows" => $rows
]);
