<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Ad Command Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f4f6f8;
            --card-bg: #ffffff;
            --text-main: #111827;
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

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 40px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 20px;
        }
        .header h1 { margin: 0; font-size: 28px; font-weight: 800; color: #1f2937; letter-spacing: -0.5px; }
        .header p { margin: 8px 0 0; color: var(--text-secondary); font-size: 15px; }

        /* CARD STYLES */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e5e7eb;
        }
        .card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }

        /* Status Borders */
        .card.action-kill { border-left: 6px solid var(--danger); }
        .card.action-scale { border-left: 6px solid var(--success); }
        .card.action-watch { border-left: 6px solid var(--warning); }
        .card.action-rotate { border-left: 6px solid #f97316; } /* Orange */
        .card.action-prepare { border-left: 6px solid var(--purple); }

        .card-header {
            padding: 24px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: #ffffff;
        }

        .ad-identity h3 { margin: 0 0 6px; font-size: 18px; font-weight: 700; line-height: 1.3; }
        .tag { 
            font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
            padding: 4px 8px; border-radius: 6px; display: inline-block; margin-right: 6px;
        }
        .tag-audience { background: #e0e7ff; color: #3730a3; }
        .tag-fatigue { background: #fee2e2; color: #991b1b; }

        .card-body { padding: 24px; }

        /* METRICS GRID */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #f3f4f6;
        }
        .metric { display: flex; flex-direction: column; }
        .metric label { font-size: 11px; color: var(--text-secondary); text-transform: uppercase; font-weight: 600; margin-bottom: 4px; }
        .metric value { font-size: 15px; font-weight: 700; color: #111; }

        /* AI CONTENT */
        .ai-section { margin-top: 20px; }
        .ai-title { 
            font-size: 12px; font-weight: 800; color: var(--text-secondary); 
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; display: block; 
        }
        
        /* Markdown Styling */
        .ai-content { font-size: 15px; color: #374151; line-height: 1.6; }
        .ai-content strong { color: #111; font-weight: 700; }
        .md-list-item { 
            margin-left: 0; padding-left: 20px; margin-bottom: 8px; position: relative; 
        }
        .md-list-item::before { 
            content: "•"; color: var(--accent-blue); font-weight: bold; font-size: 18px;
            position: absolute; left: 0; top: -3px;
        }
        .md-paragraph { margin-bottom: 16px; }
        
        /* Alerts */
        .crash-alert { 
            background-color: #fef2f2; border: 1px solid #fca5a5; 
            padding: 16px; border-radius: 12px; color: #991b1b; 
            margin-bottom: 20px; display: flex; align-items: center; gap: 12px;
        }
        .life-alert { color: var(--danger); font-weight: 600; font-size: 13px; margin-top: 4px; display: block;}

        /* Badges */
        .badge { padding: 6px 14px; border-radius: 50px; color: white; font-weight: 700; font-size: 12px; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .bg-kill { background-color: var(--danger); }
        .bg-scale { background-color: var(--success); }
        .bg-watch { background-color: #fbbf24; color: #78350f; }
        .bg-rotate { background-color: #f97316; }
        .bg-prepare { background-color: var(--purple); }

        .rewrite-box {
            margin-top: 24px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 20px;
            border-radius: 12px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🧠 AI Ad Consultant</h1>
        <p>Predictive Intelligence • Velocity Tracking • Saturation Analysis</p>
    </div>

    <?php
    // ---------------------------------------------------------
    // 1. DATABASE CONNECTION
    // ---------------------------------------------------------
    $host = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech";
    $db   = "neondb";
    $user = "neondb_owner";
    $pass = "npg_kvbAhwHVu15g"; // <--- PASTE PASSWORD HERE
    
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require;options=endpoint=ep-restless-bird-ahug88k0-pooler";

    try {
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false];
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        die("<div class='crash-alert'>❌ <strong>Database Connection Failed:</strong> " . $e->getMessage() . "</div>");
    }

    // ---------------------------------------------------------
    // 2. MARKDOWN PARSER (The Magic Part)
    // ---------------------------------------------------------
    function formatMarkdown($text) {
        if (empty($text)) return '';
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // Bold: **text**
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        
        // Headers: ### Text (Gemini often uses this)
        $text = preg_replace('/^###\s+(.*?)$/m', '<div style="font-weight:800; text-transform:uppercase; margin-top:12px; font-size:12px; color:#555;">$1</div>', $text);

        // Lists: * Item or - Item
        $text = preg_replace('/^\s*[\*•-]\s+(.*?)$/m', '<div class="md-list-item">$1</div>', $text);

        // Numbered Lists: 1. Item
        $text = preg_replace('/^\s*(\d+\.)\s+(.*?)$/m', '<div class="md-list-item"><strong>$1</strong> $2</div>', $text);

        // Paragraphs: Split by double newline
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

    // ---------------------------------------------------------
    // 3. FETCH DATA
    // ---------------------------------------------------------
    $sql = "SELECT * FROM ad_predictions 
            WHERE prediction_date > NOW() - INTERVAL '48 HOURS'
            ORDER BY 
                CASE WHEN suggested_action = 'KILL' THEN 1 
                     WHEN suggested_action LIKE '%PREPARE%' THEN 2
                     WHEN suggested_action = 'SCALE' THEN 3 
                     ELSE 4 END,
                cpa_velocity DESC,
                confidence_score DESC";

    try {
        $stmt = $pdo->query($sql);
        $predictions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        die("Query Error: " . $e->getMessage());
    }

    // ---------------------------------------------------------
    // 4. RENDER CARDS
    // ---------------------------------------------------------
    if (count($predictions) > 0) {
        foreach ($predictions as $row) {
            $action = $row['suggested_action'];
            $score  = round($row['confidence_score'] * 100);
            
            // Format Strings
            $analysis = formatMarkdown($row['ai_analysis']);
            $rewrites = formatMarkdown($row['ai_rewrites']);
            
            // CSS Classes
            $cardClass = 'action-watch'; $badgeClass = 'bg-watch';
            if ($action === 'KILL') { $cardClass = 'action-kill'; $badgeClass = 'bg-kill'; }
            if ($action === 'SCALE') { $cardClass = 'action-scale'; $badgeClass = 'bg-scale'; }
            if (strpos($action, 'ROTATE') !== false) { $cardClass = 'action-rotate'; $badgeClass = 'bg-rotate'; }
            if (strpos($action, 'PREPARE') !== false) { $cardClass = 'action-prepare'; $badgeClass = 'bg-prepare'; }

            // Logic Flags
            $isCrash = ($row['cpa_velocity'] > 0.30);
            $daysLeft = isset($row['days_remaining']) ? floatval($row['days_remaining']) : 99;
            $maxSpend = isset($row['max_efficient_spend']) ? floatval($row['max_efficient_spend']) : 0;
            $audience = isset($row['audience_type']) ? $row['audience_type'] : 'General';

            echo "<div class='card $cardClass'>";
            
            // --- HEADER ---
            echo "<div class='card-header'>";
            echo "  <div class='ad-identity'>";
            echo "    <h3>" . htmlspecialchars($row['ad_id']) . "</h3>";
            echo "    <span class='tag tag-audience'>$audience</span>";
            if ($daysLeft < 3) echo "<span class='tag tag-fatigue'>⏳ Ends in " . round($daysLeft,1) . " days</span>";
            echo "  </div>";
            echo "  <div style='text-align:right'>";
            echo "    <span class='badge $badgeClass'>$action</span>";
            echo "    <div style='font-size:11px; color:#9ca3af; margin-top:6px;'>AI Confidence: $score%</div>";
            echo "  </div>";
            echo "</div>";

            // --- BODY ---
            echo "<div class='card-body'>";

            // CRASH BANNER
            if ($isCrash) {
                $spike = round($row['cpa_velocity'] * 100);
                echo "<div class='crash-alert'>";
                echo "  <div style='font-size:24px;'>📉</div>";
                echo "  <div><strong>CRASH DETECTED:</strong> CPA spiked by {$spike}% recently. The AI recommends immediate pause to save budget.</div>";
                echo "</div>";
            }

            // METRICS GRID (Only show relevant ones)
            echo "<div class='metrics-grid'>";
            
            // Velocity
            if ($row['cpa_velocity'] != 0) {
                $vColor = $row['cpa_velocity'] > 0 ? '#dc2626' : '#16a34a';
                $vIcon  = $row['cpa_velocity'] > 0 ? '🔺' : '🔻';
                echo "<div class='metric'><label>Cost Velocity</label><value style='color:$vColor'>$vIcon " . round(abs($row['cpa_velocity']) * 100) . "%</value></div>";
            }
            
            // Max Spend (For Scale)
            if ($action == 'SCALE' && $maxSpend > 0) {
                echo "<div class='metric'><label>Max Efficient Spend</label><value style='color:#16a34a'>$" . number_format($maxSpend, 2) . "/day</value></div>";
            }

            // Life Expectancy
            if ($daysLeft < 10) {
                echo "<div class='metric'><label>Est. Lifespan</label><value>" . round($daysLeft, 1) . " Days</value></div>";
            }

            // Fatigue
            if ($row['fatigue_score'] > 0) {
                echo "<div class='metric'><label>Fatigue Score</label><value>" . round($row['fatigue_score'], 1) . "</value></div>";
            }
            echo "</div>"; // End Metrics

            // AI ANALYSIS
            echo "<div class='ai-section'>";
            echo "  <span class='ai-title'>🤖 Analysis & Diagnosis</span>";
            echo "  <div class='ai-content'>$analysis</div>";
            echo "</div>";

            // REWRITES / ACTION PLAN
            if (!empty($rewrites)) {
                echo "<div class='rewrite-box'>";
                echo "  <span class='ai-title' style='color:#2563eb'>💡 Suggested Fixes</span>";
                echo "  <div class='ai-content'>$rewrites</div>";
                echo "</div>";
            }

            echo "</div>"; // End Body
            echo "</div>"; // End Card
        }
    } else {
        echo "<div style='text-align:center; padding:60px; color:#6b7280;'>";
        echo "<h2>Waiting for Data...</h2>";
        echo "<p>No predictions found in the database. Please run <code>ad_brain.py</code> to generate insights.</p>";
        echo "</div>";
    }
    ?>
</div>

</body>
</html>