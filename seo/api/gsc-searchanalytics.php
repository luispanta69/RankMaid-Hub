<?php
/**
 * Google Search Console – Search Analytics for a single property.
 * GET param: siteUrl (required) – the GSC property URL (e.g. https://www.example.com/ or sc-domain:example.com)
 * Returns JSON: { summary, timeSeries, topQueries, topPages } or { error, message }
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$siteUrl = isset($_GET['siteUrl']) ? trim($_GET['siteUrl']) : '';
if ($siteUrl === '') {
    echo json_encode(['error' => 'bad_request', 'message' => 'Missing siteUrl parameter.']);
    exit;
}

$configPath = dirname(__DIR__) . '/config.php';
if (!is_file($configPath)) {
    echo json_encode(['error' => 'not_configured', 'message' => 'Config not found.']);
    exit;
}

$config = require $configPath;
$clientId = trim((string) ($config['client_id'] ?? ''));
$clientSecret = trim((string) ($config['client_secret'] ?? ''));
$tokenFile = $config['token_file'] ?? (__DIR__ . '/token.json');

if (empty($clientId) || empty($clientSecret) || !is_file($tokenFile)) {
    echo json_encode(['error' => 'not_authorized', 'message' => 'Connect Google Search Console first.']);
    exit;
}

$token = json_decode(file_get_contents($tokenFile), true);
if (empty($token['refresh_token'])) {
    echo json_encode(['error' => 'not_authorized', 'message' => 'Session expired. Connect GSC again.']);
    exit;
}

$accessToken = $token['access_token'] ?? null;
$expiresAt = isset($token['expires_at']) ? (int) $token['expires_at'] : 0;

if (empty($accessToken) || time() >= $expiresAt - 60) {
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST          => true,
        CURLOPT_POSTFIELDS   => http_build_query([
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $token['refresh_token'],
            'grant_type'    => 'refresh_token',
        ]),
        CURLOPT_HTTPHEADER   => ['Content-Type: application/x-www-form-urlencoded'],
    ]);
    $ref = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200) {
        echo json_encode(['error' => 'not_authorized', 'message' => 'Session expired. Connect GSC again.']);
        exit;
    }
    $ref = json_decode($ref, true);
    $accessToken = $ref['access_token'];
    $token['access_token'] = $accessToken;
    $token['expires_at'] = time() + (isset($ref['expires_in']) ? (int) $ref['expires_in'] : 3600);
    file_put_contents($tokenFile, json_encode($token));
}

$encodedSite = rawurlencode($siteUrl);
$baseUrl = 'https://www.googleapis.com/webmasters/v3/sites/' . $encodedSite . '/searchAnalytics/query';

$endDate = date('Y-m-d');
$startDate = date('Y-m-d', strtotime('-89 days'));

$headers = [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json',
];

function runQuery($baseUrl, $accessToken, $body) {
    $ch = curl_init($baseUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST          => true,
        CURLOPT_POSTFIELDS    => json_encode($body),
        CURLOPT_HTTPHEADER   => [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ],
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200) {
        return ['error' => $code, 'body' => $res];
    }
    return json_decode($res, true);
}

// 1) Summary (no dimensions)
$summaryRow = ['clicks' => 0, 'impressions' => 0, 'ctr' => 0, 'position' => 0];
$r = runQuery($baseUrl, $accessToken, [
    'startDate' => $startDate,
    'endDate'   => $endDate,
]);
if (isset($r['error'])) {
    echo json_encode([
        'error'   => 'api_error',
        'message' => 'Search Console API error: ' . (is_string($r['body']) ? $r['body'] : json_encode($r['body'])),
    ]);
    exit;
}
if (!empty($r['rows'][0])) {
    $row = $r['rows'][0];
    $summaryRow = [
        'clicks'      => (int) ($row['clicks'] ?? 0),
        'impressions' => (int) ($row['impressions'] ?? 0),
        'ctr'         => (float) ($row['ctr'] ?? 0),
        'position'    => (float) ($row['position'] ?? 0),
    ];
}

// 2) Time series by date
$timeSeries = [];
$r = runQuery($baseUrl, $accessToken, [
    'startDate' => $startDate,
    'endDate'   => $endDate,
    'dimensions' => ['date'],
]);
if (!isset($r['error']) && !empty($r['rows'])) {
    foreach ($r['rows'] as $row) {
        $timeSeries[] = [
            'date'        => $row['keys'][0] ?? '',
            'clicks'      => (int) ($row['clicks'] ?? 0),
            'impressions' => (int) ($row['impressions'] ?? 0),
            'ctr'         => (float) ($row['ctr'] ?? 0),
            'position'    => (float) ($row['position'] ?? 0),
        ];
    }
}

// 3) Top queries
$topQueries = [];
$r = runQuery($baseUrl, $accessToken, [
    'startDate' => $startDate,
    'endDate'   => $endDate,
    'dimensions' => ['query'],
    'rowLimit'  => 25,
]);
if (!isset($r['error']) && !empty($r['rows'])) {
    foreach ($r['rows'] as $row) {
        $topQueries[] = [
            'query'       => $row['keys'][0] ?? '',
            'clicks'      => (int) ($row['clicks'] ?? 0),
            'impressions' => (int) ($row['impressions'] ?? 0),
            'ctr'         => (float) ($row['ctr'] ?? 0),
            'position'    => (float) ($row['position'] ?? 0),
        ];
    }
}

// 4) Top pages
$topPages = [];
$r = runQuery($baseUrl, $accessToken, [
    'startDate' => $startDate,
    'endDate'   => $endDate,
    'dimensions' => ['page'],
    'rowLimit'  => 25,
]);
if (!isset($r['error']) && !empty($r['rows'])) {
    foreach ($r['rows'] as $row) {
        $topPages[] = [
            'page'        => $row['keys'][0] ?? '',
            'clicks'      => (int) ($row['clicks'] ?? 0),
            'impressions' => (int) ($row['impressions'] ?? 0),
            'ctr'         => (float) ($row['ctr'] ?? 0),
            'position'    => (float) ($row['position'] ?? 0),
        ];
    }
}

echo json_encode([
    'summary'    => $summaryRow,
    'timeSeries' => $timeSeries,
    'topQueries' => $topQueries,
    'topPages'   => $topPages,
    'startDate'  => $startDate,
    'endDate'    => $endDate,
]);
