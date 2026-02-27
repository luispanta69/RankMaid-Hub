<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Ad Command Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-secondary: #6b7280;
            --accent-blue: #2563eb;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --purple: #8b5cf6;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --radius: 16px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 40px 20px;
            line-height: 1.5;
        }

        .container { max-width: 1100px; margin: 0 auto; }

        .header {
            margin-bottom: 40px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 24px;
        }
        .header h1 { margin: 0; font-size: 30px; font-weight: 800; color: #111827; letter-spacing: -0.025em; }
        .header p { margin: 8px 0 0; color: var(--text-secondary); font-size: 16px; }

        /* CARD STYLES */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.2s ease;
            border: 1px solid #e5e7eb;
        }
        .card:hover { transform: translateY(-3px); box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.15); }

        /* Status Borders */
        .card.action-kill { border-left: 6px solid var(--danger); }
        .card.action-scale { border-left: 6px solid var(--success); }
        .card.action-watch { border-left: 6px solid var(--warning); }
        .card.action-fix { border-left: 6px solid #db2777; }
        .card.action-rotate { border-left: 6px solid #f97316; } 
        .card.action-zombie { border-left: 6px solid #18181b; } /* Black for Zombie */

        .card-header {
            padding: 24px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: #ffffff;
        }

        .ad-identity h3 { margin: 0 0 8px; font-size: 18px; font-weight: 700; line-height: 1.4; color: #111; }
        
        /* Tags */
        .tag { 
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            padding: 4px 10px; border-radius: 6px; display: inline-block; margin-right: 6px;
        }
        .tag-fb { background: #e7f5ff; color: #1877f2; }
        .tag-google { background: #fce8e6; color: #ea4335; }
        .tag-fatigue { background: #fee2e2; color: #991b1b; }

        .card-body { padding: 24px; }

        /* METRICS GRID */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .metric { 
            padding: 12px; border-radius: 8px; border: 1px solid #f3f4f6; background: #f9fafb;
            display: flex; flex-direction: column; justify-content: center;
        }
        .metric label { font-size: 11px; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
        .metric value { font-size: 15px; font-weight: 700; color: #111; }

        /* ALERTS */
        .crash-alert { 
            background-color: #fef2f2; border: 1px solid #fca5a5; 
            padding: 16px; border-radius: 12px; color: #991b1b; 
            margin-bottom: 24px; display: flex; align-items: center; gap: 12px;
        }
        .impact-alert {
            background-color: #f0fdf4; border: 1px solid #86efac;
            padding: 12px; border-radius: 8px; color: #166534;
            font-size: 14px; font-weight: 500; grid-column: span 2;
        }

        /* AI CONTENT */
        .ai-section { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f3f4f6; }
        .ai-title { 
            font-size: 12px; font-weight: 800; color: #9ca3af; 
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; display: block; 
        }
        
        /* Markdown Styling */
        .ai-content { font-size: 15px; color: #374151; line-height: 1.7; }
        .ai-content strong { color: #111; font-weight: 700; }
        .md-list-item { 
            margin-left: 0; padding-left: 20px; margin-bottom: 8px; position: relative; 
        }
        .md-list-item::before { 
            content: "•"; color: var(--accent-blue); font-weight: bold; font-size: 20px;
            position: absolute; left: 0; top: -6px;
        }
        .md-paragraph { margin-bottom: 16px; }
        
        .badge { padding: 6px 14px; border-radius: 50px; color: white; font-weight: 700; font-size: 12px; letter-spacing: 0.5px; }
        .bg-kill { background-color: var(--danger); }
        .bg-scale { background-color: var(--success); }
        .bg-watch { background-color: #fbbf24; color: #78350f; }
        .bg-fix { background-color: #db2777; }
        .bg-rotate { background-color: #f97316; }
        .bg-zombie { background-color: #18181b; }

        .rewrite-box {
            margin-top: 20px; background: #eff6ff; border: 1px solid #bfdbfe;
            padding: 20px; border-radius: 12px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Universal Ad Command Center</h1>
        <p>Analyzing Facebook & Google Ads • Velocity • Saturation • Quality Score</p>
    </div>

    <?php
    $host = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech";
    $db   = "neondb";
    $user = "neondb_owner";
    $pass = "npg_kvbAhwHVu15g"; // <--- PASTE PASSWORD
    
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require;options=endpoint=ep-restless-bird-ahug88k0-pooler";

    try {
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false];
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        die("<div class='crash-alert'>❌ <strong>Database Connection Failed:</strong> " . $e->getMessage() . "</div>");
    }

    function formatMarkdown($text) {
        if (empty($text)) return '';
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/^###\s+(.*?)$/m', '<div style="font-weight:800; text-transform:uppercase; margin-top:16px; font-size:12px; color:#555;">$1</div>', $text);
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/^\s*[\*•-]\s+(.*?)$/m', '<div class="md-list-item">$1</div>', $text);
        $text = preg_replace('/^\s*(\d+\.)\s+(.*?)$/m', '<div class="md-list-item"><strong>$1</strong> $2</div>', $text);
        
        $chunks = explode("\n\n", $text);
        $final = "";
        foreach ($chunks as $c) {
            if (strpos($c, 'md-list-item') === false && trim($c) !== '') {
                $final .= '<div class="md-paragraph">' . nl2br($c) . '</div>';
            } else {
                $final .= $c;
            }
        }
        return $final;
    }

    $sql = "SELECT * FROM ad_predictions 
            WHERE prediction_date > NOW() - INTERVAL '48 HOURS'
            ORDER BY 
                CASE WHEN suggested_action LIKE '%ZOMBIE%' THEN 0
                     WHEN suggested_action LIKE '%KILL%' THEN 1 
                     WHEN suggested_action LIKE 'FIX%' THEN 2
                     WHEN suggested_action LIKE '%SCALE%' THEN 3 
                     ELSE 4 END,
                cpa_velocity DESC,
                confidence_score DESC";

    try {
        $stmt = $pdo->query($sql);
        $predictions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        die("Query Error: " . $e->getMessage());
    }

    if (count($predictions) > 0) {
        foreach ($predictions as $row) {
            $action = $row['suggested_action'];
            $score  = round($row['confidence_score'] * 100);
            $platform = isset($row['platform']) ? strtolower($row['platform']) : 'facebook';
            
            $analysis = formatMarkdown($row['ai_analysis']);
            $rewrites = formatMarkdown($row['ai_rewrites']);
            
            $cardClass = 'action-watch'; $badgeClass = 'bg-watch';
            if (strpos($action, 'KILL') !== false) { $cardClass = 'action-kill'; $badgeClass = 'bg-kill'; }
            if (strpos($action, 'ZOMBIE') !== false) { $cardClass = 'action-zombie'; $badgeClass = 'bg-zombie'; }
            if (strpos($action, 'SCALE') !== false) { $cardClass = 'action-scale'; $badgeClass = 'bg-scale'; }
            if (strpos($action, 'FIX') === 0) { $cardClass = 'action-fix'; $badgeClass = 'bg-fix'; }
            if (strpos($action, 'PREPARE') !== false) { $cardClass = 'action-rotate'; $badgeClass = 'bg-rotate'; }

            $velocity = isset($row['cpa_velocity']) ? floatval($row['cpa_velocity']) : 0;
            $daysLeft = isset($row['days_remaining']) ? floatval($row['days_remaining']) : 99;
            $maxSpend = isset($row['max_efficient_spend']) ? floatval($row['max_efficient_spend']) : 0;
            $weakness = isset($row['weakest_link']) ? $row['weakest_link'] : 'None';
            $impact   = isset($row['impact_projection']) ? $row['impact_projection'] : '';
            
            $extra_metric = "";
            if ($platform == 'google') {
                if (preg_match('/Quality Score \((\d+)\)/', $row['ai_analysis'], $m)) {
                    $extra_metric = "<div class='metric'><label>Quality Score</label><value>$m[1]/10</value></div>";
                }
            }

            echo "<div class='card $cardClass'>";
            
            echo "<div class='card-header'>";
            echo "  <div class='ad-identity'>";
            echo "    <h3>" . htmlspecialchars($row['ad_id']) . "</h3>";
            
            if ($platform == 'google') echo "<span class='tag tag-google'>Google Ads</span>";
            else echo "<span class='tag tag-fb'>Facebook</span>";

            if ($platform == 'facebook' && $daysLeft < 3) echo "<span class='tag tag-fatigue'>⏳ Ends in " . round($daysLeft,1) . " days</span>";
            echo "  </div>";
            echo "  <div style='text-align:right'>";
            echo "    <span class='badge $badgeClass'>$action</span>";
            echo "    <div style='font-size:11px; color:#9ca3af; margin-top:6px;'>AI Confidence: $score%</div>";
            echo "  </div>";
            echo "</div>";

            echo "<div class='card-body'>";

            // ZOMBIE ALERT
            if (strpos($action, 'ZOMBIE') !== false) {
                echo "<div class='crash-alert' style='background:#18181b; border-color:#3f3f46; color:#e4e4e7;'>";
                echo "  <span style='font-size:24px'>🧟</span>";
                echo "  <div><strong>ZOMBIE AD DETECTED:</strong> High spend with ZERO conversions. Kill immediately.</div>";
                echo "</div>";
            }
            // CRASH ALERT
            else if ($velocity > 0.30) {
                $spike = round($velocity * 100);
                echo "<div class='crash-alert'>";
                echo "  <span style='font-size:24px'>📉</span>";
                echo "  <div><strong>CRASH DETECTED:</strong> CPA spiked {$spike}% recently. Immediate pause recommended.</div>";
                echo "</div>";
            }
            // WASTE ALERT
            if (strpos($row['ai_analysis'], 'WASTE RISK') !== false) {
                preg_match('/WASTE RISK: \$(.*?)\/week/', $row['ai_analysis'], $matches);
                $waste = isset($matches[1]) ? $matches[1] : '???';
                echo "<div class='crash-alert' style='background:#fff1f2; border-color:#fda4af; color:#be123c;'>";
                echo "  <span style='font-size:24px'>💸</span>";
                echo "  <div><strong>BUDGET BLEED:</strong> Projected waste: <strong>$$waste/week</strong>.</div>";
                echo "</div>";
            }

            echo "<div class='metrics-grid'>";

            if ($weakness !== 'None' && strpos($action, 'SCALE') === false) {
                echo "<div class='metric' style='border-color:#fecaca; background:#fef2f2;'>";
                echo "<label style='color:#b91c1c;'>⚠️ Bottleneck</label>";
                echo "<value style='color:#b91c1c; font-size:13px;'>" . htmlspecialchars($weakness) . "</value>";
                echo "</div>";
            }

            if ($velocity != 0) {
                $vColor = $velocity > 0 ? '#dc2626' : '#16a34a';
                $vIcon  = $velocity > 0 ? '🔺' : '🔻';
                echo "<div class='metric'><label>Cost Velocity</label><value style='color:$vColor'>$vIcon " . round(abs($velocity) * 100) . "%</value></div>";
            }

            if (strpos($action, 'SCALE') !== false && $maxSpend > 0) {
                echo "<div class='metric'><label>Max Efficient Spend</label><value style='color:#16a34a'>$" . number_format($maxSpend, 2) . "</value></div>";
            }
            
            if ($platform == 'facebook' && isset($row['fatigue_score'])) {
                 echo "<div class='metric'><label>Frequency</label><value>" . round($row['fatigue_score'], 2) . "</value></div>";
            }
            echo $extra_metric; 

            if (!empty($impact)) {
                echo "<div class='impact-alert'>💡 <strong>Opportunity:</strong> " . htmlspecialchars($impact) . "</div>";
            }

            echo "</div>"; 

            echo "<div class='ai-section'>";
            echo "  <span class='ai-title'>🤖 Analysis & Reasoning</span>";
            echo "  <div class='ai-content'>$analysis</div>";
            echo "</div>";

            if (!empty($rewrites)) {
                echo "<div class='rewrite-box'>";
                echo "  <span class='ai-title' style='color:#2563eb'>✨ Recommended Actions</span>";
                echo "  <div class='ai-content'>$rewrites</div>";
                echo "</div>";
            }

            echo "</div>"; 
            echo "</div>"; 
        }
    } else {
        echo "<div style='text-align:center; padding:60px; color:#6b7280;'>";
        echo "<h2>Waiting for Data...</h2>";
        echo "<p>No predictions found. Run <code>run_all.bat</code> to generate insights.</p>";
        echo "</div>";
    }
    ?>
</div>

</body>
</html>