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

// ✅ FIXED: Correct Column Mapping based on Dec-*.csv headers
// 0: Campaign, 1: Ad Set, 6: Frequency, 7: Result Type, 8: Results, 9: CPR, 10: Spend, 14: CTR
define('FB_AD_COLUMN_MAP', [
    'campaign_name'    => 0,
    'ad_set_name'      => 1,
    'frequency'        => 6,
    'result_type'      => 7,
    'results'          => 8,
    'cost_per_result'  => 9,
    'amount_spent_usd' => 10,
    'ctr_all'          => 14,
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
        $all_ads = get_and_process_ads_from_db($pdo, $start_date, $end_date);
        
        $opportunities = [];
        
        // Group by ad set to find both winners and bleeders
        $ad_sets = [];
        foreach ($all_ads as $ad) {
             $key = $ad['campaign_name'] . '|' . $ad['ad_set_name'];
             if (!isset($ad_sets[$key])) {
                 $ad_sets[$key] = [
                     'campaign' => $ad['campaign_name'],
                     'ad_set' => $ad['ad_set_name'],
                     'spend' => 0,
                     'results' => 0
                 ];
             }
             $ad_sets[$key]['spend'] += cleanNum($ad['amount_spent_usd']);
             $ad_sets[$key]['results'] += intval($ad['results']);
        }
         
        foreach ($ad_sets as $set) {
             $revenue = $set['results'] * $assumed_value;
             $roas = $set['spend'] > 0 ? $revenue / $set['spend'] : 0;
             
             // 1. Scale Opportunity (Winner): High ROAS (> 4.0) & Significant Spend (> 50)
             if ($roas > 4.0 && $set['spend'] > 50) {
                 $opportunities[] = [
                     'campaign' => $set['campaign'],
                     'ad_set' => $set['ad_set'],
                     'roas' => $roas,
                     'spend' => $set['spend'],
                     'reason' => 'High ROAS'
                 ];
             }
             // 2. Optimization Opportunity (Bleeder): Zero Results & High Spend (> 50)
             // We include this if the request asks for 'fatigue_critical' OR generally
             // Lowered threshold from >100 to >50 to be safer for short ranges
             else if ($set['results'] == 0 && $set['spend'] > 50) {
                 $opportunities[] = [
                     'campaign' => $set['campaign'],
                     'ad_set' => $set['ad_set'],
                     'roas' => 0,
                     'spend' => $set['spend'],
                     'reason' => 'Zero Results / High Spend',
                     'type' => 'bleeder'
                 ];
             }
        }
         
        // Sort strategy: 
        // If getting 'fatigue_critical' (Creative Alert), prioritize bleeders (ROAS=0) first.
        // Otherwise, prioritize Winners (High ROAS) first.
        usort($opportunities, function($a, $b) use ($type) {
             if ($type === 'fatigue_critical') {
                 // Prioritize Bleeders (ROAS = 0)
                 if ($a['roas'] == 0 && $b['roas'] > 0) return -1;
                 if ($b['roas'] == 0 && $a['roas'] > 0) return 1;
                 // If both are bleeders, sort by Spend desc
                 if ($a['roas'] == 0 && $b['roas'] == 0) return $b['spend'] <=> $a['spend'];
             }
             
             // Default (Scale Opportunities): Prioritize High ROAS
             if ($a['roas'] > 0 && $b['roas'] > 0) return $b['roas'] <=> $a['roas'];
             if ($a['roas'] > 0) return -1;
             if ($b['roas'] > 0) return 1;
             return $b['spend'] <=> $a['spend'];
        });
        
        echo json_encode([
            'success' => true,
            'data' => [
                'daily_breakdown' => $opportunities
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