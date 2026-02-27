<?php
/**
 * Bulk Import Facebook CSV Reports (Dec 7-31)
 * Removes first line from each CSV and imports into facebook_ads table
 */

require_once 'db.php'; // Use centralized PDO connection

try {
    echo "<h2>Facebook CSV Bulk Import</h2>";
    echo "<p>Importing Dec 7-31, 2025 data...</p>";
    
    // Define all CSV files to import
    $csv_files = [];
    for ($day = 7; $day <= 31; $day++) {
        $csv_files[] = "Dec-" . $day . ".csv";
    }
    
    $total_rows = 0;
    $total_imported = 0;
    $results = [];
    $grouped_data = [];

    foreach ($csv_files as $csv_file) {
        // Construct file path from Downloads
        $file_path = "C:\\Users\\scott_e6j170z\\Downloads\\Dec\\Dec\\" . $csv_file;

        if (!file_exists($file_path)) {
            $results[] = "❌ $csv_file - File not found";
            continue;
        }

        if (($handle = fopen($file_path, 'r')) !== FALSE) {
            $header_skipped = false;

            while (($data = fgetcsv($handle)) !== FALSE) {
                // Skip first line (header)
                if (!$header_skipped) {
                    $header_skipped = true;
                    continue;
                }

                $total_rows++;
                $reporting_starts = isset($data[15]) && !empty($data[15]) ? date('Y-m-d', strtotime($data[15])) : null;

                if ($reporting_starts) {
                    if (!isset($grouped_data[$reporting_starts])) {
                        $grouped_data[$reporting_starts] = [];
                    }
                    $grouped_data[$reporting_starts][] = $data;
                }
            }
            fclose($handle);
            $results[] = "✓ $csv_file - Processed";
        } else {
            $results[] = "❌ $csv_file - Could not open file";
        }
    }

    // Prepare insert statement with PDO for the new table structure
    $sql_insert = "INSERT INTO facebook_ads_data (report_date, raw_row) VALUES (?, ?)";
    $stmt_insert = $pdo->prepare($sql_insert);

    foreach ($grouped_data as $date => $rows) {
        try {
            // Check if data for this date already exists to avoid duplicates
            $check_sql = "SELECT COUNT(*) FROM facebook_ads_data WHERE report_date = ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$date]);
            if ($check_stmt->fetchColumn() == 0) {
                $stmt_insert->execute([$date, json_encode($rows)]);
                $total_imported += count($rows);
            }
        } catch (PDOException $e) {
            echo "Error inserting data for date $date: " . $e->getMessage() . "<br>";
        }
    }

    // Display results
    echo "<hr>";
    echo "<h3>Import Summary</h3>";
    echo "<ul>";
    foreach ($results as $result) {
        echo "<li>$result</li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<h3>Statistics</h3>";
    echo "<p><strong>Total rows processed:</strong> $total_rows</p>";
    echo "<p><strong>Total rows imported:</strong> $total_imported</p>";
    
    // Show current data in table
    echo "<hr>";
    echo "<h3>Current Facebook Ads Data (Daily Summary)</h3>";
    
    // Fetch raw data and process in PHP to avoid JSON_TABLE issues on older MariaDB
    $sql_fetch = "SELECT report_date, raw_row FROM facebook_ads_data ORDER BY report_date DESC LIMIT 30";
    $result = $pdo->query($sql_fetch);
    
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%;'>";
    echo "<tr style='background-color:#4CAF50; color:white;'><th>Date</th><th>Records</th><th>Campaigns</th><th>Results</th><th>Spend</th></tr>";
    
    foreach ($result as $row) {
        $report_date = $row['report_date'];
        $json_rows = json_decode($row['raw_row'], true);
        
        $total_records = 1; // 1 DB row per date
        $campaigns = 0;
        $total_results = 0;
        $total_spent = 0;

        if (is_array($json_rows)) {
            $campaigns = count($json_rows);
            foreach ($json_rows as $ad_row) {
                $total_results += intval($ad_row[9] ?? 0);
                $total_spent += floatval(preg_replace('/[^\d.]/', '', $ad_row[11] ?? '0'));
            }
        }

        echo "<tr>";
        echo "<td>" . $report_date . "</td>";
        echo "<td>" . $total_records . "</td>";
        echo "<td>" . $campaigns . "</td>";
        echo "<td>" . $total_results . "</td>";
        echo "<td>\$" . number_format($total_spent, 2) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error: " . $e->getMessage() . "</h3>";
}
?>
