<?php
header("Content-Type: application/json");
require_once "db.php";

$rangeId = $_GET["id"] ?? null;
if (!$rangeId) {
    http_response_code(400);
    echo json_encode(["error" => "Missing range id"]);
    exit;
}

// Decode range
// We re-derive the range the same way
$stmt = $pdo->query("
  SELECT report_date
  FROM facebook_ads_data
  ORDER BY report_date ASC
");

$dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (count($dates) === 0) {
    echo json_encode([]);
    exit;
}

// Build ranges again
$ranges = [];
$start = $dates[0];
$prev = $dates[0];

foreach ($dates as $i => $curr) {
    if ($i === 0)
        continue;

    $expected = date("Y-m-d", strtotime("$prev +1 day"));

    if ($curr === $expected) {
        $prev = $curr;
    } else {
        $ranges[md5($start . $prev)] = [$start, $prev];
        $start = $curr;
        $prev = $curr;
    }
}
$ranges[md5($start . $prev)] = [$start, $prev];

if (!isset($ranges[$rangeId])) {
    http_response_code(404);
    echo json_encode(["error" => "Range not found"]);
    exit;
}

[$startDate, $endDate] = $ranges[$rangeId];

// Fetch rows
$stmt = $pdo->prepare("
  SELECT raw_row
  FROM facebook_ads_data
  WHERE report_date BETWEEN ? AND ?
  ORDER BY report_date ASC
");
$stmt->execute([$startDate, $endDate]);

$rows = [];
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $decoded = json_decode($r["raw_row"], true);
    if (is_array($decoded)) {
        foreach ($decoded as $row) {
            $rows[] = $row;
        }
    }
}

echo json_encode([
    "headers" => null, // you already have headers client-side
    "rows" => $rows
]);
