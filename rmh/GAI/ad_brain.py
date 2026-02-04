import pandas as pd
import google.generativeai as genai
import json
import urllib.parse
import os
import math
from sqlalchemy import create_engine, text
from sklearn.ensemble import RandomForestClassifier
from sklearn.impute import SimpleImputer
from datetime import datetime

# ==========================================
# 1. DATABASE CONFIGURATION
# ==========================================
DB_USER = "neondb_owner" 
DB_PASS = "npg_kvbAhwHVu15g"  # <--- PASTE PASSWORD
DB_HOST = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech"
DB_NAME = "neondb"
ENDPOINT_ID = "ep-restless-bird-ahug88k0-pooler"

# 2. AI & STRATEGY SETTINGS
GEMINI_API_KEY = os.getenv("GEMINI_API_KEY", "YOUR_GEMINI_KEY_HERE")
TARGET_CPA = 50.00   # You want leads under $50
KILL_CPA = 80.00     # Kill if over $80

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
        # Base features + Advanced Ratios
        self.features = [
            'spend', 'cpm', 'ctr', 'cpc', 'frequency', 
            'day_of_week', 'cvr', 'click_lead_ratio', 'auction_competitiveness'
        ]

    def setup_database(self):
        """Creates Table and View, and ensures new columns exist."""
        print("🛠️ Checking database structure...")
        with self.engine.begin() as conn:
            # 1. Create Table (If not exists)
            conn.execute(text("""
                CREATE TABLE IF NOT EXISTS ad_predictions (
                    prediction_id SERIAL PRIMARY KEY,
                    campaign_id TEXT,
                    ad_id TEXT,
                    platform TEXT,
                    suggested_action TEXT,
                    confidence_score NUMERIC,
                    ai_analysis TEXT,
                    ai_rewrites TEXT,
                    suggested_budget NUMERIC,
                    fatigue_score NUMERIC,
                    prediction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            """))

            # 2. Patch Table: Ensure new columns exist (If table was created previously)
            # We try to add them; if they exist, Postgres ignores or we catch error
            new_cols = [
                "ai_rewrites TEXT", 
                "suggested_budget NUMERIC", 
                "fatigue_score NUMERIC"
            ]
            for col_def in new_cols:
                try:
                    conn.execute(text(f"ALTER TABLE ad_predictions ADD COLUMN IF NOT EXISTS {col_def};"))
                except Exception:
                    pass # Column likely exists or specific DB version issue

            # 3. Refresh View (Drop first to avoid 'cannot drop columns' error)
            conn.execute(text("DROP VIEW IF EXISTS active_ads_view CASCADE;"))
            
            # 4. Create View with 'cpm' included
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
                    cpm,
                    reporting_starts
                FROM campaign_data
                WHERE delivery_status = 'active';
            """))
        print("✅ Database structure is ready.")

    def fetch_data(self):
        print("📥 Fetching active ads...")
        return pd.read_sql(text("SELECT * FROM active_ads_view WHERE spend > 0"), self.engine)

    def add_advanced_features(self, df):
        """Calculates Ratios (CVR, Day of Week) for deeper analysis."""
        # 1. Day of Week (0=Mon, 6=Sun)
        if 'reporting_starts' in df.columns and not df['reporting_starts'].isnull().all():
            df['day_of_week'] = pd.to_datetime(df['reporting_starts']).dt.dayofweek
        else:
            df['day_of_week'] = datetime.now().weekday() # Fallback to today

        # 2. Conversion Rate (Leads / Clicks)
        df['cvr'] = df['conversions'] / (df['clicks'] + 1)

        # 3. Click-to-Lead Cost Ratio (CPC / CPA)
        # Avoid division by zero
        df['click_lead_ratio'] = df['clicks'] / (df['conversions'] + 1)

        # 4. Auction Competitiveness (CPM / CPC)
        df['auction_competitiveness'] = df['cpm'] / (df['cpc'] + 0.1)
        
        return df

    def train_model(self):
        print("🧠 Training Advanced AI Model...")
        # Mock Data with NEW FEATURES
        X_mock = pd.DataFrame({
            'spend':     [100, 200, 50, 300, 150],
            'cpm':       [15, 35, 10, 40, 20],
            'ctr':       [1.5, 0.5, 2.0, 0.4, 1.2],
            'cpc':       [1.0, 3.5, 0.8, 5.0, 1.5],
            'frequency': [1.1, 2.5, 1.0, 3.0, 1.5],
            'day_of_week': [0, 1, 2, 6, 4], # Mon, Tue, Wed, Sun, Fri
            'cvr':       [0.1, 0.01, 0.15, 0.02, 0.12], 
            'click_lead_ratio': [10, 100, 6, 50, 8],    
            'auction_competitiveness': [15, 10, 12, 8, 13]
        })
        y_mock = [1, 0, 1, 0, 1] # 1=Good, 0=Bad
        
        self.model = RandomForestClassifier(n_estimators=100)
        self.model.fit(X_mock[self.features], y_mock)

    def generate_fixes_with_gemini(self, ad_text, cpa):
        """Asks Gemini to rewrite the hook for failing ads."""
        if "YOUR_GEMINI" in GEMINI_API_KEY or not GEMINI_API_KEY: 
            return "AI Skipped (No Key)"
        
        prompt = f"""
        I am running a Lead Gen Ad. It is failing (CPA: ${cpa}).
        Current Ad Hook: "{ad_text}"

        Task:
        1. Diagnose the failure in 10 words.
        2. Write 3 NEW, BETTER hooks to test.
        Format: Return ONLY the 3 hooks as a bulleted list.
        """
        try:
            model = genai.GenerativeModel('gemini-1.5-pro')
            response = model.generate_content(prompt)
            return response.text.strip()
        except:
            return "AI Generation Failed"

    def calculate_smart_budget(self, current_spend, prob):
        """Suggests budget scaling based on confidence."""
        if prob > 0.9: return round(current_spend * 1.20, 2) # +20%
        if prob > 0.75: return round(current_spend * 1.10, 2) # +10%
        return 0.00

    def run_pipeline(self):
        # 1. Setup DB
        self.setup_database()

        # 2. Get Data
        df = self.fetch_data()
        if df.empty:
            print("⚠️ No active ads found with spend > 0.")
            return

        # 3. Calculate Advanced Features (The Upgrade)
        df = self.add_advanced_features(df)

        # 4. Train Model
        self.train_model()
        
        # 5. Predict
        imputer = SimpleImputer(strategy='constant', fill_value=0)
        X = pd.DataFrame(imputer.fit_transform(df[self.features]), columns=self.features)
        probs = self.model.predict_proba(X)[:, 1]

        results = []
        print(f"📊 Analyzing {len(df)} ads with Advanced Metrics...")

        for idx, row in df.iterrows():
            prob = probs[idx]
            cpa = row['cpa']
            freq = row['frequency']
            ctr = row['ctr']
            
            action = "WATCH"
            reason = ""
            rewrites = ""
            new_budget = 0.0

            # --- LOGIC ENGINE ---
            
            # Fatigue Calculation: (Freq * 5) / CTR
            # If Freq is high and CTR is low, score explodes.
            fatigue_score = (freq * 5) / (ctr + 0.01)

            if cpa > KILL_CPA:
                action = "KILL"
                reason = f"CPA ${cpa} exceeds limit."
                rewrites = self.generate_fixes_with_gemini(row['headline'], cpa)
            
            elif fatigue_score > 15:
                action = "ROTATE CREATIVE"
                reason = f"Fatigue detected (Score: {round(fatigue_score,1)}). Audience bored."
                rewrites = self.generate_fixes_with_gemini(row['headline'], cpa)

            elif prob > 0.80 and cpa < TARGET_CPA:
                action = "SCALE"
                new_budget = self.calculate_smart_budget(row['spend'], prob)
                reason = f"High Efficiency (Prob: {int(prob*100)}%). Scale budget."

            results.append({
                'campaign_id': row['campaign_id'],
                'ad_id': row['ad_id'],
                'platform': 'facebook',
                'suggested_action': action,
                'confidence_score': float(prob),
                'ai_analysis': reason,
                'ai_rewrites': rewrites,
                'suggested_budget': new_budget,
                'fatigue_score': float(fatigue_score)
            })

        # 6. Save to DB
        if results:
            final_df = pd.DataFrame(results)
            final_df.to_sql('ad_predictions', self.engine, if_exists='append', index=False)
            print("🚀 Advanced Predictions saved to DB!")
            print(final_df[['suggested_action', 'suggested_budget', 'fatigue_score']].head())

if __name__ == "__main__":
    try:
        engine = AdPredictorEngine(DB_URL)
        engine.run_pipeline()
    except Exception as e:
        print(f"❌ Fatal Error: {e}")