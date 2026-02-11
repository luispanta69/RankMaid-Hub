<?php
/**
 * Google Search Console API configuration (sample).
 * Copy this file to config.php and fill in your Google Cloud OAuth credentials.
 */

return [
    // OAuth 2.0 Client ID (from Google Cloud Console)
    'client_id'     => 'YOUR_CLIENT_ID.apps.googleusercontent.com',
    'client_secret' => 'YOUR_CLIENT_SECRET',

    // Full URL to your OAuth callback (must match the redirect URI in Google Cloud Console)
    'redirect_uri'  => 'http://localhost/RankMaid-Hub/seo/api/oauth-callback.php',

    // Path to store the refresh token (relative to this file's directory or absolute)
    'token_file'    => __DIR__ . '/api/token.json',

    // Path to Google client secret JSON (optional – if you use JSON instead of client_id/secret above)
    'credentials_json' => null,
];
