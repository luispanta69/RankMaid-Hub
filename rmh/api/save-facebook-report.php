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

    // Insert or update rows. `ON DUPLICATE KEY UPDATE` handles existing dates.
    $insertStmt = $pdo->prepare("
    INSERT INTO facebook_ads_data (report_date, raw_row)
    VALUES (:report_date, :raw_row) 
    ON CONFLICT (report_date) DO UPDATE SET raw_row = EXCLUDED.raw_row
    ");

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

    $insertedOrUpdated = 0;
    foreach ($grouped as $date => $rowsForDate) {
        $insertStmt->execute([
            ":report_date" => $date,
            ":raw_row" => json_encode($rowsForDate)
        ]);

        $insertedOrUpdated++;
    }

    $pdo->commit();

    $totalDays = count(array_unique(array_column($rows, 'report_date')));

    echo json_encode([
        "success" => true,
        "message" => "Report saved successfully.",
        "days_processed" => $insertedOrUpdated
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        "error" => "Save failed",
        "message" => $e->getMessage()
    ]);
}
