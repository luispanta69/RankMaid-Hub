# Google Search Console API – Setup

The Master List page loads all websites/properties from your Google Search Console account. To enable this, you need to create OAuth credentials in Google Cloud and configure this app.

---

## 1. Create a project in Google Cloud Console

1. Go to [Google Cloud Console](https://console.cloud.google.com/).
2. Create a new project (or select an existing one), e.g. **RankMaid SEO**.
3. Note the project name; you’ll use it when enabling the API.

---

## 2. Enable the Search Console API

1. In the Cloud Console, open **APIs & Services** → **Library**.
2. Search for **Google Search Console API**.
3. Open it and click **Enable**.

---

## 3. Configure the OAuth consent screen

1. Go to **APIs & Services** → **OAuth consent screen**.
2. Choose **External** (unless you use a Google Workspace org and want Internal).
3. Fill in:
   - **App name:** e.g. RankMaid SEO Dashboard  
   - **User support email:** your email  
   - **Developer contact:** your email  
4. Click **Save and Continue**.
5. **Where to add the scope:**
   - On the **Scopes** step, click **Add or Remove Scopes**.
   - In the filter/search box, type **“webmasters”** or **“Search Console”**.
   - If you see **“Google Search Console API”** with a sub-scope, expand it and check the read-only option, then **Update**.
   - **Or** at the bottom under “Manually add scopes”, paste this exactly and click **Add to table**:
     ```text
     https://www.googleapis.com/auth/webmasters.readonly
     ```
   - Click **Save and Continue**.
6. On **Test users** (if the app is in Testing), add your Google account so you can sign in. Then **Save and Continue**.

---

## 4. Create OAuth 2.0 credentials (same project as step 1–3)

1. In Google Cloud Console, **confirm the project in the top bar** is the one where you enabled the Search Console API and set the OAuth consent screen.
2. Go to **APIs & Services** → **Credentials**.
3. Click **Create Credentials** → **OAuth client ID**.
4. **Application type:** choose **Web application** (not Desktop app).
5. **Name:** e.g. RankMaid SEO Web.
6. Under **Authorized redirect URIs**, click **Add URI** and enter the exact URL where Google will send users after login. It must match your `redirect_uri` in config. Examples:
   - Local: `http://localhost/RankMaid-Hub/seo/api/oauth-callback.php`
   - Or: `http://your-domain.com/seo/api/oauth-callback.php`
7. Click **Create**.
8. Copy the **Client ID** (ends with `.apps.googleusercontent.com`) and **Client secret** with no extra spaces; you’ll put them in `config.php`.

---

## 5. Configure this app

1. In the `seo` folder, copy the sample config:
   - Copy `config.sample.php` to `config.php`.
2. Edit `config.php`:
   - Set `client_id` to your OAuth Client ID.
   - Set `client_secret` to your OAuth Client secret.
   - Set `redirect_uri` to the **exact** redirect URI you added in the Cloud Console (e.g. `http://localhost/RankMaid-Hub/seo/api/oauth-callback.php` or your live URL).
3. **No Composer needed** – the app uses only PHP and cURL to talk to Google’s API.
4. Ensure the web server can write to the `seo/api` folder (for storing the refresh token in `token.json`). On XAMPP this is usually already writable.

---

## 6. Use the Master List

1. Open the SEO dashboard and go to **Master List**.
2. Click **Connect GSC** in the blue banner.
3. Sign in with the Google account that has access to the Search Console properties you want.
4. Approve the requested scope (read-only Search Console access).
5. You’ll be redirected back; the Master List will load all sites/properties from that account.

---

## Troubleshooting

- **“Access blocked: The OAuth client was not found” / Error 401: invalid_client**  
  Google doesn’t recognise your Client ID. Check the following in **the same Google Cloud project** where you enabled the Search Console API:

  1. Go to [Google Cloud Console](https://console.cloud.google.com/) and **select the correct project** (top bar).
  2. Open **APIs & Services** → **Credentials**.
  3. Under **OAuth 2.0 Client IDs**, find your **Web application** client (not “Desktop app” or “Chrome app”). If there isn’t one, click **Create Credentials** → **OAuth client ID**, choose **Web application**, add your redirect URI, then **Create**.
  4. Click that client’s name to open it. Copy the **Client ID** (it looks like `123456789012-xxxxxxxxxx.apps.googleusercontent.com`). Paste it into `config.php` as `client_id` with **no extra spaces, quotes, or line breaks** inside the string. Copy the **Client secret** the same way into `client_secret`.
  5. In `config.php`, ensure values are in single quotes and the line has no syntax error, e.g.  
     `'client_id' => '123456789012-xxxxxxxxxx.apps.googleusercontent.com',`
  6. Save `config.php`, refresh the Master List page, and click **Connect GSC** again.

- **“Copy config.sample.php to config.php”**  
  Create `config.php` from `config.sample.php` and fill in `client_id`, `client_secret`, and `redirect_uri`.

- **Redirect URI mismatch**  
  The `redirect_uri` in `config.php` must match **exactly** (including path and `http`/`https`) one of the Authorized redirect URIs in the OAuth client.

- **Session expired / Token invalid**  
  Delete `seo/api/token.json` and connect again via **Connect GSC**.

- **No sites returned**  
  The Google account you used must have at least one property in [Google Search Console](https://search.google.com/search-console). Add/verify a property there first.
