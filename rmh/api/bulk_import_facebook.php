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

    // Prepare insert statement with PDO
    $sql_insert = "INSERT INTO facebook_ads 
        (campaign_name, ad_set_name, delivery_status, delivery_level, reach, impressions, frequency, 
         result_type, results, cost_per_result, amount_spent_usd, cpm, link_clicks, cpc, ctr, 
         reporting_starts, reporting_ends)
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $pdo->prepare($sql_insert);
    
    foreach ($csv_files as $csv_file) {
        // Construct file path from Downloads
        $file_path = "C:\\Users\\scott_e6j170z\\Downloads\\Dec\\Dec\\" . $csv_file;
        
        if (!file_exists($file_path)) {
            $results[] = "❌ $csv_file - File not found";
            continue;
        }
        
        if (($handle = fopen($file_path, 'r')) !== FALSE) {
            $header_skipped = false;
            $file_imported = 0;
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                // Skip first line (header)
                if (!$header_skipped) {
                    $header_skipped = true;
                    continue;
                }
                
                $total_rows++;
                
                // Map CSV columns (note: some columns may be empty)
                $campaign_name = isset($data[0]) ? trim($data[0]) : '';
                $ad_set_name = isset($data[1]) ? trim($data[1]) : '';
                $delivery_status = isset($data[2]) ? trim($data[2]) : '';
                $delivery_level = isset($data[3]) ? trim($data[3]) : '';
                $reach = isset($data[4]) ? intval(str_replace(',', '', $data[4])) : 0;
                $impressions = isset($data[5]) ? intval(str_replace(',', '', $data[5])) : 0;
                $frequency = isset($data[6]) ? floatval($data[6]) : 0;
                $result_type = isset($data[7]) ? trim($data[7]) : '';
                $results_count = isset($data[8]) ? intval(str_replace(',', '', $data[8])) : 0;
                $cost_per_result = isset($data[9]) ? floatval($data[9]) : 0;
                $amount_spent_usd = isset($data[10]) ? floatval($data[10]) : 0;
                $cpm = isset($data[11]) ? floatval($data[11]) : 0;
                $link_clicks = isset($data[12]) ? intval(str_replace(',', '', $data[12])) : 0;
                $cpc = isset($data[13]) ? floatval($data[13]) : 0;
                $ctr = isset($data[14]) ? floatval($data[14]) : 0;
                $reporting_starts = isset($data[15]) && !empty($data[15]) ? date('Y-m-d', strtotime($data[15])) : null;
                $reporting_ends = isset($data[16]) && !empty($data[16]) ? date('Y-m-d', strtotime($data[16])) : null;
                
                try {
                    $stmt_insert->execute([
                        $campaign_name, $ad_set_name, $delivery_status, $delivery_level, $reach, $impressions, $frequency,
                        $result_type, $results_count, $cost_per_result, $amount_spent_usd, $cpm, $link_clicks, $cpc, $ctr,
                        $reporting_starts, $reporting_ends
                    ]);
                    $file_imported++;
                    $total_imported++;
                } catch (PDOException $e) {
                    echo "Error in $csv_file row: " . $e->getMessage() . "<br>";
                }
            }
            
            fclose($handle);
            $results[] = "✓ $csv_file - Imported $file_imported rows";
        } else {
            $results[] = "❌ $csv_file - Could not open file";
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
    echo "<h3>Current Facebook Ads Data</h3>";
    
    $sql_count = "SELECT 
        COUNT(*) as total_records,
        DATE(reporting_starts) as report_date,
        COUNT(DISTINCT campaign_name) as campaigns,
        SUM(results) as total_results,
        SUM(amount_spent_usd) as total_spent
    FROM facebook_ads
    GROUP BY DATE(reporting_starts)
    ORDER BY report_date DESC
    LIMIT 30";
    
    $result = $pdo->query($sql_count);
    
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse; width:100%;'>";
    echo "<tr style='background-color:#4CAF50; color:white;'><th>Date</th><th>Records</th><th>Campaigns</th><th>Results</th><th>Spend</th></tr>";
    
    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . $row['report_date'] . "</td>";
        echo "<td>" . $row['total_records'] . "</td>";
        echo "<td>" . $row['campaigns'] . "</td>";
        echo "<td>" . $row['total_results'] . "</td>";
        echo "<td>\$" . number_format($row['total_spent'], 2) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error: " . $e->getMessage() . "</h3>";
}
?>
