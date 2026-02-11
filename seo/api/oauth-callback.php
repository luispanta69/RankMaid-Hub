<?php
/**
 * OAuth 2.0 callback for Google Search Console. Uses only PHP + cURL (no Composer).
 */

$configPath = dirname(__DIR__) . '/config.php';
if (!is_file($configPath)) {
    header('Location: ../index.php?gsc=config_missing');
    exit;
}

$config = require $configPath;
$tokenFile = $config['token_file'] ?? (__DIR__ . '/token.json');

if (!isset($_GET['code'])) {
    header('Location: ../index.php?gsc=denied');
    exit;
}

$clientId = trim((string) ($config['client_id'] ?? ''));
$clientSecret = trim((string) ($config['client_secret'] ?? ''));
$redirectUri = trim((string) ($config['redirect_uri'] ?? ''));

$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST          => true,
    CURLOPT_POSTFIELDS    => http_build_query([
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'code'          => $_GET['code'],
        'grant_type'    => 'authorization_code',
    ]),
    CURLOPT_HTTPHEADER    => ['Content-Type: application/x-www-form-urlencoded'],
]);
$body = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$token = json_decode($body, true);
if ($code !== 200 || empty($token['access_token'])) {
    $msg = isset($token['error_description']) ? $token['error_description'] : ($token['error'] ?? 'Unknown error');
    header('Location: ../index.php?gsc=error&msg=' . urlencode($msg));
    exit;
}

$token['expires_at'] = time() + (isset($token['expires_in']) ? (int) $token['expires_in'] : 3600);
$dir = dirname($tokenFile);
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}
file_put_contents($tokenFile, json_encode($token));

header('Location: ../index.php?gsc=connected');
