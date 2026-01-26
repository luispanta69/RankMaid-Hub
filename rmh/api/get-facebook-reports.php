<?php
header("Content-Type: application/json");
require_once "db.php";

/*
 We derive "reports" from existing daily data.
 Each report = continuous date range.
*/

$stmt = $pdo->query("
  SELECT report_date
  FROM facebook_ads
  ORDER BY report_date ASC
");

$dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!$dates) {
    echo json_encode([]);
    exit;
}

// Build contiguous ranges
$ranges = [];
$start = $dates[0];
$prev = $dates[0];

for ($i = 1; $i < count($dates); $i++) {
    $curr = $dates[$i];
    $expected = date("Y-m-d", strtotime("$prev +1 day"));

    if ($curr === $expected) {
        $prev = $curr;
    } else {
        $ranges[] = [
            "id" => md5($start . $prev),
            "report_start" => $start,
            "report_end" => $prev
        ];
        $start = $curr;
        $prev = $curr;
    }
}

// Push last range
$ranges[] = [
    "id" => md5($start . $prev),
    "report_start" => $start,
    "report_end" => $prev
];

echo json_encode($ranges);
