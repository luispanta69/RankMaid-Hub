<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Ad Brain</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; vertical-align: top; }
        th { background-color: #333; color: white; }
        .badge { padding: 5px 10px; border-radius: 4px; color: white; font-weight: bold; font-size: 0.9em; display: inline-block; }
        .badge-kill { background-color: #dc3545; }
        .badge-scale { background-color: #28a745; }
        .badge-watch { background-color: #ffc107; color: #333; }
        .badge-rotate { background-color: #fd7e14; }
        .analysis-box { font-size: 0.9em; color: #555; margin-top: 5px; }
        .rewrite-box { background: #f8f9fa; border-left: 3px solid #007bff; padding: 10px; margin-top: 8px; font-size: 0.85em; }
        .budget-box { color: #155724; background-color: #d4edda; padding: 5px; border-radius: 4px; font-weight: bold; margin-top: 5px; display: inline-block;}
    </style>
</head>
<body>

<div class="container">
    <h1>🧠 AI Ad Consultant</h1>
    <p>Real-time predictions based on your Campaign Data.</p>

    <?php
    // 1. DATABASE CONFIGURATION
    $host = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech";
    $db   = "neondb";
    $user = "neondb_owner";
    $pass = "npg_kvbAhwHVu15g"; // <--- UPDATE THIS
    
    // We add 'options' to the DSN to handle the endpoint ID correctly
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require;options=endpoint=ep-restless-bird-ahug88k0-pooler";

    try {
        // We disable emulation to prevent some caching issues
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false];
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        die("<div style='color:red'>❌ Connection failed: " . $e->getMessage() . "</div>");
    }

    // 2. FETCH DATA (Explicitly listing columns to prevent "cached plan" errors)
    $sql = "SELECT 
                ad_id, 
                suggested_action, 
                confidence_score, 
                ai_analysis, 
                ai_rewrites, 
                suggested_budget, 
                fatigue_score
            FROM ad_predictions 
            WHERE prediction_date > NOW() - INTERVAL '48 HOURS'
            ORDER BY prediction_date DESC";

    try {
        $stmt = $pdo->query($sql);
        $predictions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        die("Query Error: " . $e->getMessage());
    }

    // 3. DISPLAY TABLE
    if (count($predictions) > 0) {
        echo "<table>";
        echo "<thead><tr><th width='30%'>Ad Name</th><th width='15%'>Action</th><th>AI Insights</th></tr></thead>";
        echo "<tbody>";

        foreach ($predictions as $row) {
            $action = $row['suggested_action'];
            $score  = round($row['confidence_score'] * 100) . "%";
            $analysis = htmlspecialchars($row['ai_analysis']);
            
            // Determine Badge Color
            $badgeClass = 'badge-watch';
            if ($action === 'KILL') $badgeClass = 'badge-kill';
            if ($action === 'SCALE') $badgeClass = 'badge-scale';
            if ($action === 'ROTATE CREATIVE') $badgeClass = 'badge-rotate';

            echo "<tr>";
            
            // Col 1: Name
            echo "<td><strong>" . htmlspecialchars($row['ad_id']) . "</strong>";
            if ($row['fatigue_score'] > 10) {
                echo "<br><small style='color:orange;'>⚠️ High Fatigue Score: " . round($row['fatigue_score'], 1) . "</small>";
            }
            echo "</td>";

            // Col 2: Action Badge
            echo "<td><span class='badge $badgeClass'>$action</span><br><small>Conf: $score</small></td>";

            // Col 3: Analysis & extras
            echo "<td>";
            echo "<div class='analysis-box'>$analysis</div>";

            // Show Budget if Scale
            if ($action === 'SCALE' && $row['suggested_budget'] > 0) {
                echo "<div class='budget-box'>💰 Increase Budget to $" . $row['suggested_budget'] . "/day</div>";
            }

            // Show Rewrites if Kill/Rotate
            if (!empty($row['ai_rewrites'])) {
                echo "<div class='rewrite-box'><strong>💡 Try these Hooks:</strong><br>" . nl2br(htmlspecialchars($row['ai_rewrites'])) . "</div>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No predictions found. Run the Python script to generate data.</p>";
    }
    ?>
</div>

</body>
</html>