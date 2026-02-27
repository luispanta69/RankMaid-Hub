<?php
header("Content-Type: application/json");
require_once "db.php";

try {

    // ✅ Pull latest 31 days from DB
    $stmt = $pdo->query("
        SELECT report_date, raw_row
        FROM google_ads_data
        ORDER BY report_date DESC
        LIMIT 31
    ");

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode([
            "success" => true,
            "headers" => [],
            "rows" => [],
            "start" => null,
            "end" => null
        ]);
        exit;
    }

    // ✅ Sort oldest → newest
    $dates = array_column($data, "report_date");
    sort($dates);

    $start = $dates[0];
    $end = $dates[count($dates) - 1];

    // ✅ Convert raw_row JSON into row arrays
    $rows = array_map(function ($r) {
        $decoded = json_decode($r["raw_row"], true);

        // ✅ If stored as object {report_date,row}
        return $decoded["row"] ?? $decoded;
    }, $data);

    echo json_encode([
        "success" => true,
        "headers" => [], // Optional: load from DB if stored
        "rows" => $rows,
        "start" => $start,
        "end" => $end
    ]);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Failed to load latest Google range",
        "message" => $e->getMessage()
    ]);
}
