<?php
/**
 * Facebook Analysis API
 * Handles: Summary stats, ROAS analysis, and Creative Fatigue/CPA Alerts
 */

header('Content-Type: application/json');

require_once 'db.php'; // Use centralized PDO connection

// Helper to clean currency/numbers from CSV string data (e.g., "$1,200.50" -> 1200.50)
function cleanNum($val) {
    if (is_null($val) || $val === '') return 0;
    if (is_numeric($val)) return (float) $val; // Return numeric values directly
    return (float) preg_replace('/[^\d.]/', '', $val);
}

// Define a constant map for CSV column indexes to improve clarity and maintainability.
define('FB_AD_COLUMN_MAP', [
    'campaign_name'    => 0,
    'ad_set_name'      => 1,
    'frequency'        => 7,
    'result_type'      => 8,
    'results'          => 9,
    'cost_per_result'  => 10,
    'amount_spent_usd' => 11,
    'ctr_all'          => 17, // Corrected from 18 to 17 based on data sample
]);

// Helper function to get all ads from the database within a date range
// and process them in PHP. This avoids using JSON_TABLE which is not supported
// correctly in older MariaDB versions.
function get_and_process_ads_from_db($pdo, $start_date, $end_date) {
    $sql = "SELECT report_date, raw_row FROM facebook_ads_data";
    $params = [];
    if ($start_date && $end_date) {
        $sql .= " WHERE report_date BETWEEN :start_date AND :end_date";
        $params[':start_date'] = $start_date;
        $params[':end_date'] = $end_date;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $db_rows = $stmt->fetchAll();

    $all_ads = [];
    foreach ($db_rows as $db_row) {
        $report_date = $db_row['report_date'];
        $json_rows = json_decode($db_row['raw_row'], true);
        if (is_array($json_rows)) {
            foreach ($json_rows as $ad_row) {
                // Append the date to each ad record for later use
                // Map numeric indexes to associative keys for clarity and robustness
                $all_ads[] = [
                    'report_date'      => $report_date,
                    'campaign_name'    => $ad_row[FB_AD_COLUMN_MAP['campaign_name']] ?? '',
                    'ad_set_name'      => $ad_row[FB_AD_COLUMN_MAP['ad_set_name']] ?? '',
                    'frequency'        => $ad_row[FB_AD_COLUMN_MAP['frequency']] ?? 0,
                    'result_type'      => $ad_row[FB_AD_COLUMN_MAP['result_type']] ?? '',
                    'results'          => $ad_row[FB_AD_COLUMN_MAP['results']] ?? 0,
                    'cost_per_result'  => $ad_row[FB_AD_COLUMN_MAP['cost_per_result']] ?? 0,
                    'amount_spent_usd' => $ad_row[FB_AD_COLUMN_MAP['amount_spent_usd']] ?? 0,
                    'ctr_all'          => $ad_row[FB_AD_COLUMN_MAP['ctr_all']] ?? 0,
                ];
            }
        }
    }
    return $all_ads;
}


try {
    // Get parameters
    $action = isset($_GET['action']) ? $_GET['action'] : 'getSummary';
    $type   = isset($_GET['type']) ? $_GET['type'] : 'general'; // 'fatigue', 'cpa', or 'general'
    
    // Date range parameters
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    $assumed_value = isset($_GET['assumed_value']) ? floatval($_GET['assumed_value']) : 150000;
    
    // ---------------------------------------------------------
    // ACTION: GET SUMMARY (Top Level Stats)
    // ---------------------------------------------------------
    if ($action === 'getSummary') {
        $all_ads = get_and_process_ads_from_db($pdo, null, null); // Get all data

        $total_ads = count($all_ads);
        $total_results = 0;
        $total_spend = 0;
        $cpr_sum = 0;
        $latest_date = null;

        foreach($all_ads as $ad) {
            $total_results += intval($ad['results'] ?? 0);
            $total_spend += cleanNum($ad['amount_spent_usd'] ?? 0);
            $cpr_sum += cleanNum($ad['cost_per_result'] ?? 0);
            if ($latest_date === null || $ad['report_date'] > $latest_date) {
                $latest_date = $ad['report_date'];
            }
        }
        $avg_cpr = $total_ads > 0 ? $cpr_sum / $total_ads : 0;

        $total_revenue = $total_results * $assumed_value;
        $roas          = $total_spend > 0 ? $total_revenue / $total_spend : 0;
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_ads'     => $total_ads,
                'total_results' => $total_results,
                'total_spend'   => $total_spend,
                'total_revenue' => $total_revenue,
                'roas'          => $roas,
                'avg_cpr'       => $avg_cpr,
                'latest_date'   => $latest_date,
                'assumed_value_per_result' => $assumed_value
            ]
        ]);

    // ---------------------------------------------------------
    // ACTION: GET DETAILED ANALYSIS (The "Analyze" Button Logic)
    // ---------------------------------------------------------
    } else if ($action === 'getDetailedAnalysis') {
        
        // 1. Get data and process in PHP
        $all_ads = get_and_process_ads_from_db($pdo, $start_date, $end_date);

        // 2. Filter in PHP
        $filtered_ads = [];
        foreach ($all_ads as $ad) {
            $frequency = cleanNum($ad['frequency'] ?? 0);
            $ctr = cleanNum($ad['ctr_all'] ?? 0);
            $cpr = cleanNum($ad['cost_per_result'] ?? 0);
            $results = intval($ad['results'] ?? 0);
            $spend = cleanNum($ad['amount_spent_usd'] ?? 0);

            $passes_filter = false;
            if ($type === 'fatigue') {
                if ($frequency > 1.05 && $ctr < 1.0) $passes_filter = true;
            } elseif ($type === 'fatigue_critical') {
                if ($frequency > 4.5 && $ctr <= 0.6) $passes_filter = true;
            } elseif ($type === 'cpa') {
                if (($cpr > 100) || ($results == 0 && $spend > 50)) $passes_filter = true;
            } elseif ($type === 'roas_sustained') {
                $roas = $spend > 0 ? (($results * $assumed_value) / $spend) : 0;
                if ($roas > 6.0) $passes_filter = true;
            } else {
                $passes_filter = true; // 'general'
            }

            if ($passes_filter) {
                $filtered_ads[] = $ad;
            }
        }

        // 3. Sort and limit in PHP
        usort($filtered_ads, function($a, $b) {
            return cleanNum($b['amount_spent_usd'] ?? 0) <=> cleanNum($a['amount_spent_usd'] ?? 0); // Sort by spend descending
        });
        $raw_data = array_slice($filtered_ads, 0, 50);

        $processed_data = [];
        
        // 5. Process Data (Safe Math)
        foreach ($raw_data as $row) {
            $spend   = cleanNum($row['amount_spent_usd'] ?? 0);
            $results = intval($row['results'] ?? 0);
            $freq    = cleanNum($row['frequency'] ?? 0);
            $ctr     = cleanNum($row['ctr_all'] ?? 0);
            
            // Handle Division by Zero / Empty Results
            if ($results > 0) {
                $revenue = $results * $assumed_value;
                $roas = $spend > 0 ? $revenue / $spend : 0;
                $real_cpa = $spend / $results;
            } else {
                $revenue = 0;
                $roas = 0;
                $real_cpa = $spend; // If 0 results, treat CPA as Spend (infinite cost)
            }

            $processed_data[] = [
                'date'      => $row['report_date'],
                'ad_set'    => $row['ad_set_name'] ?? '',
                'campaign'  => $row['campaign_name'] ?? '',
                'results'   => $results,
                'spend'     => $spend,
                'revenue'   => $revenue,
                'roas'      => $roas,
                'cpa'       => $real_cpa,
                'frequency' => $freq,
                'ctr'       => $ctr
            ];
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'daily_breakdown' => $processed_data,
                'sustained_found' => count($processed_data) > 0,
                'best_window' => true, // Kept for frontend compatibility
                'assumed_value_per_result' => $assumed_value
            ]
        ]);
        
    // ---------------------------------------------------------
    // ACTION: GET CAMPAIGN BREAKDOWN
    // ---------------------------------------------------------
    } else if ($action === 'getCampaignDetails') {
        $all_ads = get_and_process_ads_from_db($pdo, null, null);

        $campaign_stats = [];
        foreach ($all_ads as $ad) {
            $name = $ad['campaign_name'] ?? 'Untitled Campaign';
            if (!isset($campaign_stats[$name])) {
                $campaign_stats[$name] = [
                    'name' => $name, 'ad_count' => 0, 'total_results' => 0, 'total_spend' => 0,
                    'cpr_sum' => 0, 'start_date' => $ad['report_date'], 'end_date' => $ad['report_date']
                ];
            }
            $campaign_stats[$name]['ad_count']++;
            $campaign_stats[$name]['total_results'] += intval($ad['results'] ?? 0);
            $campaign_stats[$name]['total_spend'] += cleanNum($ad['amount_spent_usd'] ?? 0);
            $campaign_stats[$name]['cpr_sum'] += cleanNum($ad['cost_per_result'] ?? 0);
            if ($ad['report_date'] < $campaign_stats[$name]['start_date']) $campaign_stats[$name]['start_date'] = $ad['report_date'];
            if ($ad['report_date'] > $campaign_stats[$name]['end_date']) $campaign_stats[$name]['end_date'] = $ad['report_date'];
        }

        $campaigns = [];
        foreach ($campaign_stats as $stats) {
            $total_spend   = floatval($stats['total_spend']);
            $total_results = intval($stats['total_results']);
            $revenue       = $total_results * $assumed_value;
            $roas          = $total_spend > 0 ? $revenue / $total_spend : 0;
            $avg_cpr       = $stats['ad_count'] > 0 ? $stats['cpr_sum'] / $stats['ad_count'] : 0;
            
            $campaigns[] = [
                'name'       => $stats['name'],
                'ads'        => intval($stats['ad_count']),
                'results'    => $total_results,
                'spend'      => $total_spend,
                'revenue'    => $revenue,
                'roas'       => $roas,
                'avg_cpr'    => $avg_cpr,
                'start_date' => $stats['start_date'],
                'end_date'   => $stats['end_date']
            ];
        }

        // Sort by spend descending
        usort($campaigns, function($a, $b) {
            return $b['spend'] <=> $a['spend'];
        });
        
        echo json_encode([
            'success' => true,
            'data' => [
                'campaigns' => $campaigns,
                'assumed_value_per_result' => $assumed_value
            ]
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>