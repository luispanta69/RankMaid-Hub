<?php
header("Content-Type: application/json");
require_once "db.php";
session_start();

// Read JSON payload
$payload = json_decode(file_get_contents("php://input"), true);

if (!$payload || empty($payload["rows"])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid payload"]);
    exit;
}

// Store headers in session
if (!empty($payload["headers"])) {
    $_SESSION["facebook_csv_headers"] = $payload["headers"];
}

$rows = $payload["rows"];

try {
    $pdo->beginTransaction();

    // ✅ UPSERT with duplicate detection
    $insertStmt = $pdo->prepare("
    INSERT INTO facebook_ads_data (report_date, headers, raw_row)
    VALUES (:report_date, :headers, :raw_row)
    ON CONFLICT (report_date)
    DO UPDATE SET raw_row = EXCLUDED.raw_row,
                  headers = EXCLUDED.headers
    RETURNING xmax
    ");

    $inserted = 0;
    $skipped = 0;

    // Group rows by date
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

    // Insert each grouped date
    foreach ($grouped as $date => $rowsForDate) {

        $jsonData = json_encode($rowsForDate);

        $insertStmt->execute([
            ":report_date" => $date,
            ":headers" => json_encode($payload["headers"]),
            ":raw_row" => $jsonData
        ]);

        $result = $insertStmt->fetch(PDO::FETCH_ASSOC);

        // ✅ xmax tells if inserted or updated
        if ($result["xmax"] == 0) {
            $inserted++;
        } else {
            $skipped++;
        }
    }

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "inserted_days" => $inserted,
        "skipped_days" => $skipped,
        "total_days" => $inserted + $skipped
    ]);

} catch (Exception $e) {
    $pdo->rollBack();

    http_response_code(500);
    echo json_encode([
        "error" => "Save failed",
        "message" => $e->getMessage()
    ]);
}
