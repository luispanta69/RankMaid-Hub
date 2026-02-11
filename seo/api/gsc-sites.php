<?php
/**
 * Google Search Console – List sites (properties) for the authenticated user.
 * Uses only PHP + cURL (no Composer required).
 * Returns JSON: { "sites": [...] } or { "error": "...", "auth_url": "..." or null }
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$configPath = dirname(__DIR__) . '/config.php';
if (!is_file($configPath)) {
    echo json_encode([
        'error'    => 'not_configured',
        'message'  => 'Copy config.sample.php to config.php and add your Google OAuth credentials.',
        'auth_url' => null,
    ]);
    exit;
}

$config = require $configPath;
$clientId = trim((string) ($config['client_id'] ?? ''));
$clientSecret = trim((string) ($config['client_secret'] ?? ''));
$redirectUri = trim((string) ($config['redirect_uri'] ?? ''));
$tokenFile = $config['token_file'] ?? (__DIR__ . '/token.json');

if (empty($clientId) || empty($clientSecret) || $clientId === 'YOUR_CLIENT_ID.apps.googleusercontent.com') {
    echo json_encode([
        'error'    => 'not_configured',
        'message'  => 'Edit config.php and add your Client ID and Client Secret from Google Cloud Console.',
        'auth_url' => null,
    ]);
    exit;
}
if (strpos($clientId, '.apps.googleusercontent.com') === false) {
    echo json_encode([
        'error'    => 'not_configured',
        'message'  => 'Client ID in config.php must end with .apps.googleusercontent.com. Copy it from Google Cloud Console → Credentials → your OAuth 2.0 Client ID.',
        'auth_url' => null,
    ]);
    exit;
}
if (empty($redirectUri) || strpos($redirectUri, 'http') !== 0) {
    echo json_encode([
        'error'    => 'not_configured',
        'message'  => 'redirect_uri in config.php must be the full URL (e.g. http://localhost/.../seo/api/oauth-callback.php) and must match the Authorized redirect URI in Google Cloud Console exactly.',
        'auth_url' => null,
    ]);
    exit;
}

$scope = 'https://www.googleapis.com/auth/webmasters.readonly';
$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id'     => $clientId,
    'redirect_uri'  => $redirectUri,
    'response_type' => 'code',
    'scope'         => $scope,
    'access_type'   => 'offline',
    'prompt'        => 'consent',
]);

// No token yet – tell frontend to show Connect button
if (!is_file($tokenFile)) {
    echo json_encode([
        'error'    => 'not_authorized',
        'message'  => 'Connect your Google Search Console account to load properties.',
        'auth_url' => $authUrl,
    ]);
    exit;
}

$token = json_decode(file_get_contents($tokenFile), true);
if (empty($token['refresh_token'])) {
    @unlink($tokenFile);
    echo json_encode([
        'error'    => 'not_authorized',
        'message'  => 'Session expired. Please connect Google Search Console again.',
        'auth_url' => $authUrl,
    ]);
    exit;
}

$accessToken = $token['access_token'] ?? null;
$expiresAt = isset($token['expires_at']) ? (int) $token['expires_at'] : 0;

// Refresh if expired (tokens typically last 1 hour)
if (empty($accessToken) || time() >= $expiresAt - 60) {
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST          => true,
        CURLOPT_POSTFIELDS    => http_build_query([
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $token['refresh_token'],
            'grant_type'    => 'refresh_token',
        ]),
        CURLOPT_HTTPHEADER    => ['Content-Type: application/x-www-form-urlencoded'],
    ]);
    $ref = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200) {
        @unlink($tokenFile);
        echo json_encode([
            'error'    => 'not_authorized',
            'message'  => 'Session expired. Please connect Google Search Console again.',
            'auth_url' => $authUrl,
        ]);
        exit;
    }
    $ref = json_decode($ref, true);
    $accessToken = $ref['access_token'];
    $token['access_token'] = $accessToken;
    $token['expires_at'] = time() + (isset($ref['expires_in']) ? (int) $ref['expires_in'] : 3600);
    file_put_contents($tokenFile, json_encode($token));
}

// List sites
$ch = curl_init('https://www.googleapis.com/webmasters/v3/sites');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER    => [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
    ],
]);
$body = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code === 401 || $code === 403) {
    @unlink($tokenFile);
    echo json_encode([
        'error'    => 'not_authorized',
        'message'  => 'Session expired. Please connect Google Search Console again.',
        'auth_url' => $authUrl,
    ]);
    exit;
}

if ($code !== 200) {
    echo json_encode([
        'error'   => 'api_error',
        'message' => 'Google API error: ' . trim($body),
    ]);
    exit;
}

$data = json_decode($body, true);
$siteEntries = [];
if (!empty($data['siteEntry']) && is_array($data['siteEntry'])) {
    foreach ($data['siteEntry'] as $entry) {
        $siteEntries[] = [
            'siteUrl'         => $entry['siteUrl'] ?? '',
            'permissionLevel' => $entry['permissionLevel'] ?? '',
        ];
    }
}

echo json_encode([
    'sites' => $siteEntries,
]);
