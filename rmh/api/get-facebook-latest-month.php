<?php
header("Content-Type: application/json");

require_once "db.php";
session_start();

// ✅ Define static headers as a fallback
// These must match the structure of the CSVs imported via bulk_import_facebook.php
$static_headers = [
    "Campaign name",
    "Ad set name",
    "Ad name",
    "Delivery status",
    "Ad set delivery",
    "Reach",
    "Impressions",
    "Frequency",
    "Result type",
    "Results",
    "Cost per result",
    "Amount spent (USD)",
    "Ends",
    "Schedule",
    "CTR (link click-through rate)",
    "Link clicks",
    "Cost per link click (USD)",
    "CTR (all)",
    "Video plays at 75%",
    "Reporting starts",
    "Reporting ends"
];

try {
    $dateToTry = new DateTime();
    $foundMonth = null;
    $start = null;
    $end = null;

    // Loop backwards for up to 12 months to find a month with data
    for ($i = 0; $i < 12; $i++) {
        $currentMonthStart = $dateToTry->format('Y-m-01');
        $currentMonthEnd = $dateToTry->format('Y-m-t');

        $checkStmt = $pdo->prepare("SELECT 1 FROM facebook_ads_data WHERE report_date BETWEEN ? AND ? LIMIT 1");
        $checkStmt->execute([$currentMonthStart, $currentMonthEnd]);

        if ($checkStmt->fetch()) {
            $foundMonth = $dateToTry->format('F Y');
            $start = $currentMonthStart;
            $end = $currentMonthEnd;
            break; // Found data, exit loop
        }

        // Go to the previous month
        $dateToTry->modify('first day of last month');
    }

    // If no data was found in the last 12 months, return empty
    if (!$foundMonth) {
        echo json_encode([
            "headers" => $_SESSION["facebook_csv_headers"] ?? $static_headers,
            "rows" => [],
            "month" => null,
            "start" => null,
            "end" => null
        ]);
        exit;
    }

    // Found a month, now load all rows for that month
    $stmt = $pdo->prepare("
      SELECT raw_row
      FROM facebook_ads_data
      WHERE report_date BETWEEN ? AND ?
      ORDER BY report_date ASC
    ");
    
    $stmt->execute([$start, $end]);
    
    $rows = [];
    // ✅ Prioritize session headers, but fall back to static headers
    $headers = $_SESSION["facebook_csv_headers"] ?? $static_headers;
    
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $raw = $r["raw_row"];
        $dayRows = is_string($raw) ? json_decode($raw, true) : $raw;
    
        if (is_array($dayRows)) {
            foreach ($dayRows as $row) {
                $rows[] = $row;
            }
        }
    }
    
    echo json_encode([
        "headers" => $headers,
        "rows" => $rows,
        "month" => $foundMonth,
        "start" => $start,
        "end" => $end
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Database query failed.",
        "message" => $e->getMessage()
    ]);
    exit;
}
