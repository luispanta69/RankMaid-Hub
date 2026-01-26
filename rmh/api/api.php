<?php
require_once 'db.php'; // Your existing connection file

$action = $_GET['action'] ?? 'global';

switch($action) {
    case 'global':
        $res['kpis'] = $pdo->query("SELECT * FROM kpi_stats")->fetchAll();
        $res['velocity'] = $pdo->query("SELECT velocity_value FROM revenue_velocity ORDER BY sort_order")->fetchAll(PDO::FETCH_COLUMN);
        $res['feed'] = $pdo->query("SELECT * FROM ai_feed")->fetchAll();
        break;
        
    case 'channel':
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM channels WHERE id = ?");
        $stmt->execute([$id]);
        $res['stats'] = $stmt->fetch();
        
        $stmt = $pdo->prepare("SELECT * FROM optimizations WHERE channel_id = ?");
        $stmt->execute([$id]);
        $res['opts'] = $stmt->fetchAll();
        break;

    case 'knowledge':
        $res['items'] = $pdo->query("SELECT * FROM knowledge_hub")->fetchAll();
        break;
}

header('Content-Type: application/json');
echo json_encode($res);