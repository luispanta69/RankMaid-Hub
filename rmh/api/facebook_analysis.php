<?php
/**
 * Facebook ROAS Analysis API
 * Pulls real data from facebook_ads table in grumpyhare__wp_dfy25 database
 */

header('Content-Type: application/json');

require_once 'db.php'; // Use centralized PDO connection

try {
    // Get the action parameter
    $action = isset($_GET['action']) ? $_GET['action'] : 'getSummary';
    $campaign_id = isset($_GET['campaign_id']) ? $_GET['campaign_id'] : 'facebook';

    // Date range parameters for detailed analysis
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    $assumed_value = isset($_GET['assumed_value']) ? floatval($_GET['assumed_value']) : 150000;
    
    if ($action === 'getSummary') {
        // Get summary stats for a channel
        $sql = "SELECT 
                    COUNT(*) as total_ads,
                    SUM(results) as total_results,
                    SUM(amount_spent_usd) as total_spend,
                    AVG(cost_per_result) as avg_cpr,
                    MAX(starts_date) as latest_date
                FROM facebook_ads";
        
        $stmt = $pdo->query($sql);
        $row = $stmt->fetch();
        
        $total_results = intval($row['total_results']);
        $total_spend = floatval($row['total_spend']);
        $total_revenue = $total_results * $assumed_value;
        $roas = $total_spend > 0 ? $total_revenue / $total_spend : 0;
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_ads' => intval($row['total_ads']),
                'total_results' => $total_results,
                'total_spend' => $total_spend,
                'total_revenue' => floatval($total_revenue),
                'roas' => floatval($roas),
                'avg_cpr' => floatval($row['avg_cpr']),
                'latest_date' => $row['latest_date'],
                'assumed_value_per_result' => floatval($assumed_value)
            ]
        ]);
        
    } else if ($action === 'getDetailedAnalysis') {
        // Get 14-day sustained analysis
        $sql = "SELECT
                    DATE(starts_date) as campaign_date,
                    COUNT(*) as ad_count,
                    SUM(results) as daily_results,
                    SUM(amount_spent_usd) as daily_spend,
                    AVG(cost_per_result) as avg_cpr,
                    GROUP_CONCAT(DISTINCT campaign_name SEPARATOR ', ') as campaigns
                FROM facebook_ads";

        $where_clauses = ["starts_date != '0000-00-00'"];
        $params = [];
        $param_types = '';

        if ($start_date && $end_date) {
            $where_clauses[] = "starts_date BETWEEN ? AND ?";
            $param_types .= 'ss';
            $params[] = $start_date;
            $params[] = $end_date;
        }

        $sql .= " WHERE " . implode(' AND ', $where_clauses);
        $sql .= " GROUP BY DATE(starts_date) ORDER BY campaign_date DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll();
        $dates_data = array();
        
        foreach ($result as $row) {
            $daily_revenue = floatval($row['daily_results']) * $assumed_value;
            $daily_spend = floatval($row['daily_spend']);
            $daily_roas = $daily_spend > 0 ? $daily_revenue / $daily_spend : 0;
            
            $dates_data[] = [
                'date' => $row['campaign_date'],
                'results' => intval($row['daily_results']),
                'spend' => floatval($daily_spend),
                'revenue' => floatval($daily_revenue),
                'roas' => floatval($daily_roas),
                'avg_cpr' => floatval($row['avg_cpr']),
                'ad_count' => intval($row['ad_count']),
                'campaigns' => $row['campaigns']
            ];
        }
        
        // If no data is found for the specified range, try to fetch all data
        if (empty($dates_data) && ($start_date && $end_date)) { // Only fallback if a specific range was requested and yielded no results
            // Re-run query without date filters to get all available data, and capture the query for debugging
            $fallback_sql = "SELECT DATE(starts_date) as campaign_date, COUNT(*) as ad_count, SUM(results) as daily_results, SUM(amount_spent_usd) as daily_spend, AVG(cost_per_result) as avg_cpr, GROUP_CONCAT(DISTINCT campaign_name SEPARATOR ', ') as campaigns FROM facebook_ads WHERE starts_date BETWEEN '2025-01-01' AND '2025-12-31' GROUP BY DATE(starts_date) ORDER BY campaign_date DESC";
            $fallback_stmt = $pdo->query($fallback_sql);
            while ($row = $fallback_stmt->fetch()) {
                $daily_revenue = floatval($row['daily_results']) * $assumed_value;
                $daily_spend = floatval($row['daily_spend']);
                $daily_roas = $daily_spend > 0 ? $daily_revenue / $daily_spend : 0;

                $dates_data[] = [
                    'date' => $row['campaign_date'],
                    'results' => intval($row['daily_results']),
                    'spend' => floatval($daily_spend),
                    'revenue' => floatval($daily_revenue),
                    'roas' => floatval($daily_roas),
                    'avg_cpr' => floatval($row['avg_cpr']),
                    'ad_count' => intval($row['ad_count']),
                    'campaigns' => $row['campaigns']
                ];
            }
        }

        // Reverse to get chronological order
        $dates_data = array_reverse($dates_data);
        
        // Check for 14-day sustained > 6.0x
        $sustained_windows = array();
        $dates_array = array_keys($dates_data);
        
        for ($i = 0; $i <= count($dates_data) - 14; $i++) { // Ensure at least 14 days for a window
            $window_dates = array_slice($dates_data, $i, 14);
            $all_above_6x = true;
            $avg_roas = 0;
            $total_spend_window = 0;
            $total_results_window = 0;
            
            foreach ($window_dates as $day) {
                $avg_roas += $day['roas'];
                $total_spend_window += $day['spend'];
                $total_results_window += $day['results'];
                if ($day['roas'] <= 6.0) {
                    $all_above_6x = false;
                }
            }
            
            $avg_roas = $avg_roas / 14;
            
            if ($all_above_6x) {
                $sustained_windows[] = [
                    'start_date' => $window_dates[0]['date'],
                    'end_date' => $window_dates[13]['date'],
                    'avg_roas' => $avg_roas,
                    'total_spend' => $total_spend_window,
                    'total_results' => $total_results_window
                ];
            }
        }
        
        // Find best 14-day window
        $best_window = null;
        $highest_avg = 0;
        
        if (count($dates_data) >= 14) { // Ensure at least 14 days for a window
            for ($i = 0; $i <= count($dates_data) - 14; $i++) {
                $window_dates = array_slice($dates_data, $i, 14);
                $avg_roas = 0;
                foreach ($window_dates as $day) {
                    $avg_roas += $day['roas'];
                }
                $avg_roas = $avg_roas / 14;
                
                if ($avg_roas > $highest_avg) {
                    $highest_avg = $avg_roas;
                    $best_window = [
                        'start_date' => $window_dates[0]['date'],
                        'end_date' => $window_dates[13]['date'],
                        'avg_roas' => $avg_roas
                    ];
                }
            }
        } else if (count($dates_data) > 0) { // Only calculate if there's some data
            // Not enough data for 14-day window, use what we have
            $avg_roas = 0;
            foreach ($dates_data as $day) {
                $avg_roas += $day['roas'];
            }
            $avg_roas = count($dates_data) > 0 ? $avg_roas / count($dates_data) : 0;
            
            if (count($dates_data) > 0) {
                $best_window = [
                    'start_date' => $dates_data[0]['date'],
                    'end_date' => $dates_data[count($dates_data) - 1]['date'],
                    'avg_roas' => $avg_roas, // This will be 0 if no data
                    'note' => 'Only ' . count($dates_data) . ' days of data available (less than 14)'
                ];
            }
        }

        // Add a note if data was fetched outside the requested range
        if ($best_window && empty($best_window['note']) && ($start_date && $end_date) && count($dates_data) > 0 && ($dates_data[0]['date'] < $start_date || $dates_data[count($dates_data)-1]['date'] > $end_date)) {
             $best_window['note'] = 'No data found for the selected date range. Displaying all available data instead.';
        }
        
        echo json_encode([
            'success' => true,
            'data' => [
                'daily_breakdown' => $dates_data,
                'sustained_windows' => $sustained_windows,
                'sustained_found' => count($sustained_windows) > 0,
                'best_window' => $best_window,
                'highest_avg_roas' => $highest_avg,
                'assumed_value_per_result' => $assumed_value,
                'total_days_analyzed' => count($dates_data)
            ]
        ]);
        
    } else if ($action === 'getCampaignDetails') {
        // Get breakdown by campaign
        $sql = "SELECT 
                    campaign_name,
                    COUNT(*) as ad_count,
                    SUM(results) as total_results,
                    SUM(amount_spent_usd) as total_spend,
                    AVG(cost_per_result) as avg_cpr,
                    MIN(starts_date) as start_date,
                    MAX(starts_date) as end_date
                FROM facebook_ads
                GROUP BY campaign_name
                ORDER BY total_spend DESC";
        
        $stmt = $pdo->query($sql);
        $campaigns = array();
        
        while ($row = $stmt->fetch()) {
            $total_results = intval($row['total_results']);
            $total_spend = floatval($row['total_spend']);
            $revenue = $total_results * $assumed_value;
            $roas = $total_spend > 0 ? $revenue / $total_spend : 0;
            
            $campaigns[] = [
                'name' => $row['campaign_name'],
                'ads' => intval($row['ad_count']),
                'results' => $total_results,
                'spend' => $total_spend,
                'revenue' => floatval($revenue),
                'roas' => floatval($roas),
                'avg_cpr' => floatval($row['avg_cpr']),
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => [
                'campaigns' => $campaigns,
                'assumed_value_per_result' => floatval($assumed_value)
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
