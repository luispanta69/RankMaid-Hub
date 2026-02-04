<?php
// 1. Connect to Postgres (Update with your credentials)
$host = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech";
$db   = "neondb";
$user = "neondb_owner";
$pass = "npg_kvbAhwHVu15g"; 
$dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require;options=endpoint=ep-restless-bird-ahug88k0-pooler";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (\PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// 2. Fetch Latest Predictions
$sql = "SELECT * FROM ad_predictions 
        WHERE prediction_date > NOW() - INTERVAL '24 HOURS'
        ORDER BY confidence_score DESC";
$stmt = $pdo->query($sql);
$predictions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Display the Table
echo "<h2>🤖 AI Recommendations for Today</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<thead><tr><th>Ad Name</th><th>Action</th><th>Confidence</th><th>AI Analysis</th></tr></thead>";
echo "<tbody>";

foreach ($predictions as $row) {
    $action = $row['suggested_action'];
    $score  = round($row['confidence_score'] * 100) . "%";
    $reason = htmlspecialchars($row['ai_analysis']);
    
    // Style the badges
    $badgeColor = 'grey';
    if ($action === 'KILL') $badgeColor = '#ffcccc'; // Red
    if ($action === 'SCALE') $badgeColor = '#ccffcc'; // Green

    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['ad_id']) . "</td>";
    echo "<td style='background-color: $badgeColor; font-weight:bold;'>$action</td>";
    echo "<td>$score</td>";
    echo "<td>$reason</td>";
    echo "</tr>";
}
echo "</tbody></table>";
?>