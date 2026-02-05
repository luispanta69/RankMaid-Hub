<?php
header("Content-Type: application/json");
require_once "db.php";

$latestStmt = $pdo->query("
  SELECT MAX(report_date) AS latest_date
  FROM facebook_ads_data
");

$latest = $latestStmt->fetch(PDO::FETCH_ASSOC)["latest_date"];

if (!$latest) {
    echo json_encode([
        "headers" => [],
        "rows" => [],
        "month" => null
    ]);
    exit;
}

$start = date("Y-m-01", strtotime($latest));
$end = date("Y-m-t", strtotime($latest));

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

    // ✅ Pull headers from DB (first row only)
    if (empty($headers) && !empty($r["headers"])) {
        $headers = json_decode($r["headers"], true);
    }

    // ✅ Decode rows
    $dayRows = json_decode($r["raw_row"], true);

    if (is_array($dayRows)) {
        foreach ($dayRows as $row) {
            $rows[] = $row;
        }
    }
}

echo json_encode([
    "headers" => $headers,
    "rows" => $rows,
    "month" => date("F Y", strtotime($latest)),
    "start" => $start,
    "end" => $end
]);
