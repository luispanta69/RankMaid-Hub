import pandas as pd
import google.generativeai as genai
import json
import urllib.parse
import os
from sqlalchemy import create_engine, text
from sklearn.ensemble import RandomForestClassifier
from sklearn.impute import SimpleImputer

# ==========================================
# 1. DATABASE CONFIGURATION
# ==========================================
DB_USER = "neondb_owner" 
DB_PASS = "npg_kvbAhwHVu15g"  # <--- PASTE YOUR PASSWORD
DB_HOST = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech"
DB_NAME = "neondb"
ENDPOINT_ID = "ep-restless-bird-ahug88k0-pooler"

# 2. SETTINGS
GEMINI_API_KEY = os.getenv("GEMINI_API_KEY", "AIzaSyCxdmqcA7WYOOHoaeqT_Lpg13Fpwc9ucvY")
TARGET_CPA = 50.00   
KILL_CPA = 80.00     

# Configure Gemini
genai.configure(api_key=GEMINI_API_KEY)

# Construct Secure URL
encoded_pass = urllib.parse.quote_plus(DB_PASS)
DB_URL = (
    f"postgresql://{DB_USER}:{encoded_pass}@{DB_HOST}:5432/{DB_NAME}"
    f"?sslmode=require&options=endpoint%3D{ENDPOINT_ID}"
)

class AdPredictorEngine:
    def __init__(self, db_url):
        self.engine = create_engine(db_url)
        # Features required by the model
        self.features = ['spend', 'cpm', 'ctr', 'cpc', 'frequency']

    def setup_database(self):
        """Creates the necessary Table and View if they don't exist."""
        print("🛠️ Checking database structure...")
        with self.engine.begin() as conn:
            # 1. Create Output Table
            conn.execute(text("""
                CREATE TABLE IF NOT EXISTS ad_predictions (
                    prediction_id SERIAL PRIMARY KEY,
                    campaign_id TEXT,
                    ad_id TEXT,
                    platform TEXT,
                    suggested_action TEXT,
                    confidence_score NUMERIC,
                    ai_analysis TEXT,
                    prediction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            """))

            # 2. Drop old view to allow updates
            print("   - Refreshing View definition...")
            conn.execute(text("DROP VIEW IF EXISTS active_ads_view CASCADE;"))

            # 3. Create View (ADDED 'cpm' HERE)
            conn.execute(text("""
                CREATE VIEW active_ads_view AS
                SELECT 
                    ad_set_name as ad_id,
                    campaign_name as campaign_id,
                    'facebook' as platform,
                    ad_set_name as headline,       
                    amount_spent_usd as spend,
                    impressions,
                    link_clicks as clicks,
                    results as conversions,
                    COALESCE(cost_per_result, 0) as cpa,
                    ctr,
                    cpc,
                    frequency,
                    cpm  -- <--- FIXED: Added this column
                FROM campaign_data
                WHERE delivery_status = 'active';
            """))
        print("✅ Database structure is ready.")

    def fetch_data(self):
        print("📥 Fetching active ads...")
        return pd.read_sql(text("SELECT * FROM active_ads_view WHERE spend > 0"), self.engine)

    def train_model(self):
        print("🧠 Training AI Model...")
        # Mock Training Data
        X_mock = pd.DataFrame({
            'spend': [100, 200, 50, 300, 150],
            'cpm':   [15, 35, 10, 40, 20],
            'ctr':   [1.5, 0.5, 2.0, 0.4, 1.2],
            'cpc':   [1.0, 3.5, 0.8, 5.0, 1.5],
            'frequency': [1.1, 2.5, 1.0, 3.0, 1.5]
        })
        y_mock = [1, 0, 1, 0, 1] 
        
        self.model = RandomForestClassifier(n_estimators=50)
        self.model.fit(X_mock[self.features], y_mock)

    def analyze_text_with_gemini(self, ad_text):
        if "YOUR_GEMINI" in GEMINI_API_KEY or not GEMINI_API_KEY: 
            return "AI Skipped (No Key)"
        try:
            model = genai.GenerativeModel('gemini-1.5-pro')
            prompt = f"Critique this ad hook: '{ad_text}'. Why might it fail? 1 sentence."
            response = model.generate_content(prompt)
            return response.text.strip()
        except Exception as e:
            return f"AI Analysis Failed: {e}"

    def run_pipeline(self):
        self.setup_database()
        
        df = self.fetch_data()
        if df.empty:
            print("⚠️ No active ads found with spend > 0.")
            return

        self.train_model()
        
        # Prepare Data
        imputer = SimpleImputer(strategy='constant', fill_value=0)
        X = pd.DataFrame(imputer.fit_transform(df[self.features]), columns=self.features)
        
        # Predict
        probs = self.model.predict_proba(X)[:, 1]

        results = []
        print(f"📊 Analyzing {len(df)} ads...")

        for idx, row in df.iterrows():
            prob = probs[idx]
            cpa = row['cpa']
            action = "WATCH"
            reason = ""

            if cpa > KILL_CPA:
                action = "KILL"
                reason = f"CPA (${cpa}) is too high. " + self.analyze_text_with_gemini(row['headline'])
            elif prob < 0.30:
                action = "KILL"
                reason = f"Algorithm predicts failure (Low CTR: {row['ctr']}%)."
            elif prob > 0.80 and cpa < TARGET_CPA:
                action = "SCALE"
                reason = "High probability of cheap leads. Scale budget."
            else:
                reason = "Performance stable."

            results.append({
                'campaign_id': row['campaign_id'],
                'ad_id': row['ad_id'],
                'platform': 'facebook',
                'suggested_action': action,
                'confidence_score': float(prob),
                'ai_analysis': reason
            })

        if results:
            final_df = pd.DataFrame(results)
            final_df.to_sql('ad_predictions', self.engine, if_exists='append', index=False)
            print("🚀 Predictions saved to 'ad_predictions' table!")
            print("\n--- RESULTS PREVIEW ---")
            print(final_df[['ad_id', 'suggested_action', 'ai_analysis']].head())

if __name__ == "__main__":
    try:
        engine = AdPredictorEngine(DB_URL)
        engine.run_pipeline()
    except Exception as e:
        print(f"❌ Fatal Error: {e}")