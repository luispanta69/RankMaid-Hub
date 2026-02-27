<?php
/**
 * RMH | Core API Router
 * Handles: Global Dashboard, Channel Stats, and Knowledge Hub
 */

header('Content-Type: application/json');

// Ensure we are connecting to the database from the same folder
require_once 'db.php'; 

try {
    // 1. Get the action parameter (default to global)
    $action = isset($_GET['action']) ? $_GET['action'] : 'global';
    $res = []; // Initialize response array

    switch($action) {
        // ---------------------------------------------------------
        // GLOBAL DASHBOARD DATA
        // ---------------------------------------------------------
        case 'global':
            // Fetch high-level KPIs
            $stmt = $pdo->query("SELECT * FROM kpi_stats LIMIT 1");
            $res['kpis'] = $stmt->fetch();

            // Fetch Revenue Velocity data
            $stmt = $pdo->query("SELECT velocity_value FROM revenue_velocity ORDER BY sort_order ASC");
            $res['velocity'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Fetch AI Feed items
            $stmt = $pdo->query("SELECT * FROM ai_feed ORDER BY created_at DESC LIMIT 5");
            $res['feed'] = $stmt->fetchAll();
            break;
            
        // ---------------------------------------------------------
        // CHANNEL SPECIFIC DATA (Generic)
        // ---------------------------------------------------------
        case 'channel':
            $id = isset($_GET['id']) ? $_GET['id'] : null;
            
            if ($id) {
                // Get Channel Stats
                $stmt = $pdo->prepare("SELECT * FROM channels WHERE id = ?");
                $stmt->execute([$id]);
                $res['stats'] = $stmt->fetch();
                
                // Get Optimizations for this channel
                $stmt = $pdo->prepare("SELECT * FROM optimizations WHERE channel_id = ?");
                $stmt->execute([$id]);
                $res['opts'] = $stmt->fetchAll();
            } else {
                throw new Exception("Channel ID is required");
            }
            break;

        // ---------------------------------------------------------
        // KNOWLEDGE HUB
        // ---------------------------------------------------------
        case 'knowledge':
            $stmt = $pdo->query("SELECT * FROM knowledge_hub ORDER BY category, title");
            $res['items'] = $stmt->fetchAll();
            break;

        default:
            throw new Exception("Invalid Action Request");
    }

    // Return the JSON response
    echo json_encode(['success' => true, 'data' => $res]);

} catch (Exception $e) {
    // Handle any DB or Logic errors gracefully
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?>