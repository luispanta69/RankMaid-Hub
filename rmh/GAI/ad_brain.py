import pandas as pd
import google.generativeai as genai
import json
import urllib.parse
import os
import time
import numpy as np
import re
from sqlalchemy import create_engine, text
from sklearn.ensemble import RandomForestClassifier
from sklearn.impute import SimpleImputer
from datetime import datetime

# ==========================================
# 1. CONFIGURATION
# ==========================================
DB_USER = "neondb_owner" 
DB_PASS = "npg_kvbAhwHVu15g" # <--- PASTE PASSWORD HERE
DB_HOST = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech"
DB_NAME = "neondb"
ENDPOINT_ID = "ep-restless-bird-ahug88k0-pooler"

# API & THRESHOLDS
GEMINI_API_KEY = os.getenv("GEMINI_API_KEY", "AIzaSyCxdmqcA7WYOOHoaeqT_Lpg13Fpwc9ucvY")
TARGET_CPA = 50.00   # Goal: Leads under $50
KILL_CPA = 80.00     # Kill: Leads over $80
FATIGUE_LIMIT = 2.5  # Frequency where ads usually die

# Configure AI
if GEMINI_API_KEY and "YOUR_" not in GEMINI_API_KEY:
    genai.configure(api_key=GEMINI_API_KEY)

# Database Connection String
encoded_pass = urllib.parse.quote_plus(DB_PASS)
DB_URL = (
    f"postgresql://{DB_USER}:{encoded_pass}@{DB_HOST}:5432/{DB_NAME}"
    f"?sslmode=require&options=endpoint%3D{ENDPOINT_ID}"
)

class AdPredictorEngine:
    def __init__(self, db_url):
        self.engine = create_engine(db_url)
        self.active_model_name = None 
        # Full Feature Set for the Brain
        self.features = [
            'spend', 'cpm', 'ctr', 'cpc', 'frequency', 
            'day_of_week', 'cvr', 'click_lead_ratio', 
            'cpa_velocity', 'keyword_score', 'audience_score'
        ]

    def get_valid_model_name(self):
        """
        Dynamically finds a working Gemini model to prevent 404 Errors.
        """
        if self.active_model_name: return self.active_model_name
        
        print("🔍 Scanning for available Gemini models...")
        try:
            for m in genai.list_models():
                if 'generateContent' in m.supported_generation_methods:
                    # Prefer Flash (Fastest) -> Then Pro
                    if 'flash' in m.name:
                        self.active_model_name = m.name
                        return m.name
            
            # Fallback
            return 'models/gemini-1.5-flash'
        except:
            return 'gemini-1.5-flash'

    def setup_database(self):
        """Auto-heals the database structure (Tables + Columns + Views)."""
        print("🛠️ Checking database structure...")
        with self.engine.begin() as conn:
            # 1. Create Main Prediction Table
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
                    cpa_velocity NUMERIC,
                    days_remaining NUMERIC,
                    max_efficient_spend NUMERIC,
                    audience_type TEXT,
                    prediction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            """))

            # 2. Add columns if missing (Evolution Support)
            cols = ["cpa_velocity", "ai_rewrites", "suggested_budget", "fatigue_score", 
                    "days_remaining", "max_efficient_spend", "audience_type"]
            for col in cols:
                type_def = "TEXT" if col == "audience_type" else "NUMERIC"
                try:
                    conn.execute(text(f"ALTER TABLE ad_predictions ADD COLUMN IF NOT EXISTS {col} {type_def};"))
                except: pass 

            # 3. Refresh the View
            conn.execute(text("DROP VIEW IF EXISTS active_ads_view CASCADE;"))
            
            # 4. Create the Standardized View
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
        # Get data sorted by date for velocity calculations
        return pd.read_sql(text("SELECT * FROM active_ads_view WHERE spend > 0 ORDER BY reporting_starts ASC"), self.engine)

    def classify_audience(self, ad_name):
        """Extracts audience type from naming convention."""
        name = str(ad_name).lower()
        if 'lookalike' in name or 'lal' in name: return 'Lookalike'
        if 'retargeting' in name or 'warm' in name or 'visit' in name: return 'Retargeting'
        if 'broad' in name or 'open' in name: return 'Broad'
        if 'interest' in name or '+' in name: return 'Interest'
        return 'General'

    def calculate_advanced_metrics(self, df):
        """Feature Engineering: Velocity, Life Expectancy, Saturation."""
        print("📈 Calculating Advanced Predictive Metrics...")
        
        # 1. CPA VELOCITY (The Crash Detector)
        df = df.sort_values(by=['ad_id', 'reporting_starts'])
        df['prev_cpa'] = df.groupby('ad_id')['cpa'].shift(1)
        df['cpa_velocity'] = (df['cpa'] - df['prev_cpa']) / (df['prev_cpa'] + 0.01)
        df['cpa_velocity'] = df['cpa_velocity'].fillna(0)

        # 2. AUDIENCE SEGMENTATION
        df['audience_type'] = df['ad_id'].apply(self.classify_audience)
        audience_map = {'Lookalike': 1, 'Retargeting': 2, 'Broad': 3, 'Interest': 1, 'General': 0}
        df['audience_score'] = df['audience_type'].map(audience_map)

        # 3. LIFE EXPECTANCY (Days Remaining)
        # Calculate daily frequency growth. If freq grows 0.1/day, and limit is 2.5...
        df['freq_growth'] = df.groupby('ad_id')['frequency'].diff()
        df['freq_growth'] = df['freq_growth'].fillna(0.05) 
        df['days_remaining'] = (FATIGUE_LIMIT - df['frequency']) / (df['freq_growth'].replace(0, 0.05))
        df['days_remaining'] = df['days_remaining'].clip(lower=0)

        # 4. MAX EFFICIENT SPEND (Saturation Ceiling)
        # Buffer of 0.8 ensures we don't spend right up to the breaking point
        df['spend_headroom'] = (TARGET_CPA / (df['cpa'] + 0.01)) * 0.8
        df['max_efficient_spend'] = df['spend'] * df['spend_headroom']

        # 5. KEYWORD SCORING
        def score_keywords(text):
            s = 0
            t = str(text).lower()
            if 'video' in t: s += 1
            if 'offer' in t: s += 1
            if 'test' in t: s -= 1
            return s
        df['keyword_score'] = df['headline'].apply(score_keywords)

        # 6. EFFICIENCY RATIOS
        if 'reporting_starts' in df.columns and not df['reporting_starts'].isnull().all():
            df['day_of_week'] = pd.to_datetime(df['reporting_starts']).dt.dayofweek
        else:
            df['day_of_week'] = datetime.now().weekday()

        df['cvr'] = df['conversions'] / (df['clicks'] + 1)
        df['click_lead_ratio'] = df['clicks'] / (df['conversions'] + 1)
        
        return df

    def train_model(self):
        print("🧠 Training AI Model...")
        # Mock Data with all new features
        X_mock = pd.DataFrame({
            'spend': [100, 200, 50], 'cpm': [15, 35, 10], 'ctr': [1.5, 0.5, 2.0],
            'cpc': [1.0, 3.5, 0.8], 'frequency': [1.1, 2.5, 1.0], 'day_of_week': [0, 1, 2],
            'cvr': [0.1, 0.01, 0.15], 'click_lead_ratio': [10, 100, 6],
            'cpa_velocity': [0.1, 0.5, -0.2], 'keyword_score': [1, 0, 2],
            'audience_score': [1, 2, 3]
        })
        y_mock = [1, 0, 1] 
        self.model = RandomForestClassifier(n_estimators=100)
        self.model.fit(X_mock[self.features], y_mock)

    def generate_gemini_analysis(self, row, context_type="general"):
        if not GEMINI_API_KEY or "YOUR_" in GEMINI_API_KEY: return "AI Skipped (No Key)"
        
        # Rate Limit Protection
        time.sleep(2)

        prompt = ""
        if context_type == "crash":
            prompt = f"Ad '{row['headline']}' CPA spiked {int(row['cpa_velocity']*100)}% to ${row['cpa']}. Why? 1 sentence."
        elif context_type == "fatigue":
            prompt = f"Ad '{row['headline']}' is dying (Days Left: {int(row['days_remaining'])}). Suggest a creative refresh."
        else:
            prompt = f"Ad '{row['headline']}' is performing well. Why is this hook effective? 1 sentence."

        try:
            model_name = self.get_valid_model_name()
            model = genai.GenerativeModel(model_name)
            response = model.generate_content(prompt)
            return response.text.strip()
        except Exception as e:
            print(f"⚠️ Gemini Error: {e}")
            return f"AI Error: {str(e)[:50]}..."

    def run_pipeline(self):
        # 1. Setup
        self.setup_database()

        # 2. Fetch & Enrich
        df = self.fetch_data()
        if df.empty:
            print("⚠️ No active ads found with spend > 0.")
            return

        df = self.calculate_advanced_metrics(df)

        # 3. Train
        self.train_model()
        
        # 4. Predict
        imputer = SimpleImputer(strategy='constant', fill_value=0)
        X = pd.DataFrame(imputer.fit_transform(df[self.features]), columns=self.features)
        probs = self.model.predict_proba(X)[:, 1]

        results = []
        print(f"📊 Analyzing {len(df)} ads...")

        # Analyze LATEST snapshot only
        latest_status = df.groupby('ad_id').tail(1)

        for idx, row in latest_status.iterrows():
            prob = probs[0] if len(probs) > 0 else 0.5
            
            cpa = row['cpa']
            velocity = row['cpa_velocity']
            days_left = row['days_remaining']
            max_spend = row['max_efficient_spend']
            
            action = "WATCH"
            reason = ""
            rewrites = ""
            
            # --- DECISION LOGIC ---
            
            # 1. CRASH (Velocity > 30%)
            if velocity > 0.30:
                action = "KILL"
                reason = f"CRASH: CPA spiked {int(velocity*100)}%."
                reason += " " + self.generate_gemini_analysis(row, "crash")
            
            # 2. DEATH APPROACHING (Life Expectancy < 2 days)
            elif days_left < 2 and cpa < KILL_CPA:
                action = "PREPARE NEW CREATIVE" 
                reason = f"Ad will saturate in ~{int(days_left)} days."
                rewrites = self.generate_gemini_analysis(row, "fatigue")

            # 3. EXPENSIVE
            elif cpa > KILL_CPA:
                action = "KILL"
                reason = f"CPA ${cpa} too high."

            # 4. WINNER
            elif prob > 0.75 and cpa < TARGET_CPA:
                action = "SCALE"
                reason = f"Winner ({row['audience_type']}). Scale until ${int(max_spend)}."
                
            else:
                reason = "Performance stable."

            results.append({
                'campaign_id': row['campaign_id'],
                'ad_id': row['ad_id'],
                'platform': 'facebook',
                'suggested_action': action,
                'confidence_score': float(prob),
                'ai_analysis': reason,
                'ai_rewrites': rewrites,
                'suggested_budget': round(max_spend, 2),
                'fatigue_score': 0, 
                'cpa_velocity': float(velocity),
                'days_remaining': float(days_left),
                'max_efficient_spend': float(max_spend),
                'audience_type': row['audience_type']
            })

        # 5. Save Results
        if results:
            final_df = pd.DataFrame(results)
            final_df.to_sql('ad_predictions', self.engine, if_exists='append', index=False)
            print("🚀 Oracle Predictions saved!")
            print(final_df[['ad_id', 'suggested_action', 'days_remaining']].head(3))

if __name__ == "__main__":
    try:
        engine = AdPredictorEngine(DB_URL)
        engine.run_pipeline()
    except Exception as e:
        print(f"❌ Fatal Error: {e}")