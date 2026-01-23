<?php
header("Content-Type: application/json");
require_once "db.php";
session_start();


$payload = json_decode(file_get_contents("php://input"), true);

if (
    !$payload ||
    empty($payload["rows"])
) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid payload"]);
    exit;
}

// ✅ NOW payload exists
if (!empty($payload["headers"])) {
    $_SESSION["facebook_csv_headers"] = $payload["headers"];
}

$rows = $payload["rows"];

try {
    $pdo->beginTransaction();

    // 1. Load existing dates
    $existingDates = [];
    $stmt = $pdo->query("SELECT report_date FROM facebook_ads_data");
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $d) {
        $existingDates[$d] = true;
    }

    // 2. Insert rows (deduped)
    $insertStmt = $pdo->prepare("
    INSERT INTO facebook_ads_data (report_date, raw_row)
    VALUES (:report_date, :raw_row)
    ON DUPLICATE KEY UPDATE
      raw_row = VALUES(raw_row)
    ");

    $inserted = 0;
    $skipped = 0;

    $grouped = [];

    foreach ($rows as $item) {
        $date = $item["report_date"] ?? null;
        if (!$date)
            continue;

        if (!isset($grouped[$date])) {
            $grouped[$date] = [];
        }

        $grouped[$date][] = $item["row"];
    }

    foreach ($grouped as $date => $rowsForDate) {

        if (isset($existingDates[$date])) {
            $skipped++;
            continue;
        }

        $insertStmt->execute([
            ":report_date" => $date,
            ":raw_row" => json_encode($rowsForDate)
        ]);

        $inserted++;
    }

    $pdo->commit();

    $totalDays = count(array_unique(array_column($rows, 'report_date')));

    echo json_encode([
        "success" => true,
        "inserted_days" => $inserted,
        "skipped_days" => $totalDays - $inserted,
        "total_days" => $totalDays
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        "error" => "Save failed",
        "message" => $e->getMessage()
    ]);
}
