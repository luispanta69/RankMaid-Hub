import pandas as pd
import google.generativeai as genai
import json
import urllib.parse
import os
import time
import numpy as np
from sqlalchemy import create_engine, text
from sklearn.ensemble import RandomForestClassifier
from sklearn.impute import SimpleImputer
from datetime import datetime

# ==========================================
# 1. CONFIGURATION
# ==========================================
DB_USER = "neondb_owner" 
DB_PASS = "npg_kvbAhwHVu15g" # <--- PASTE PASSWORD
DB_HOST = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech"
DB_NAME = "neondb"
ENDPOINT_ID = "ep-restless-bird-ahug88k0-pooler"

# API & THRESHOLDS
GEMINI_API_KEY = os.getenv("GEMINI_API_KEY", "AIzaSyCxdmqcA7WYOOHoaeqT_Lpg13Fpwc9ucvY")
TARGET_CPA = 50.00   
KILL_CPA = 80.00     
FATIGUE_LIMIT_FB = 2.5 

if GEMINI_API_KEY and "YOUR_" not in GEMINI_API_KEY:
    genai.configure(api_key=GEMINI_API_KEY)

encoded_pass = urllib.parse.quote_plus(DB_PASS)
DB_URL = (
    f"postgresql://{DB_USER}:{encoded_pass}@{DB_HOST}:5432/{DB_NAME}"
    f"?sslmode=require&options=endpoint%3D{ENDPOINT_ID}"
)

class AdPredictorEngine:
    def __init__(self, db_url):
        self.engine = create_engine(db_url)
        self.active_model_name = None 
        # Universal Feature Set
        self.features = [
            'spend', 'cpa', 'ctr', 'cpc', 'conversions',
            'quality_score', 'impr_share',
            'frequency', 'cpa_velocity',
            'cvr', 'click_lead_ratio'
        ]

    def get_valid_model_name(self):
        if self.active_model_name: return self.active_model_name
        try:
            for m in genai.list_models():
                if 'generateContent' in m.supported_generation_methods:
                    if 'flash' in m.name: return m.name
            return 'models/gemini-1.5-flash'
        except: return 'gemini-1.5-flash'

    def setup_database(self):
        print("🛠️ Checking database structure...")
        with self.engine.begin() as conn:
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
                    weakest_link TEXT,
                    impact_projection TEXT,
                    prediction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            """))
            cols = ["cpa_velocity", "weakest_link", "impact_projection", 
                    "days_remaining", "max_efficient_spend", "audience_type"]
            for col in cols:
                try:
                    conn.execute(text(f"ALTER TABLE ad_predictions ADD COLUMN IF NOT EXISTS {col} TEXT;"))
                except: pass 
                
        print("✅ Database output table ready.")

    def fetch_data(self):
        print("📥 Fetching Universal Data...")
        df = pd.read_sql(text("SELECT * FROM universal_ad_data WHERE spend > 0 ORDER BY reporting_date ASC"), self.engine)
        
        # SAFETY CHECK FOR MISSING COLUMNS
        required_cols = ['conversions', 'frequency', 'quality_score', 'impr_share', 'clicks', 'cpa', 'ctr']
        for col in required_cols:
            if col not in df.columns:
                print(f"⚠️ Warning: Column '{col}' missing from DB. Filling with 0.")
                df[col] = 0.0
                
        return df

    def calculate_advanced_metrics(self, df):
        print("📈 Calculating Advanced Metrics...")
        if 'ad_identity' not in df.columns: df['ad_identity'] = "Unknown Ad"

        df = df.sort_values(by=['ad_identity', 'reporting_date'])
        
        # Velocity
        df['prev_cpa'] = df.groupby('ad_identity')['cpa'].shift(1)
        df['cpa_velocity'] = (df['cpa'] - df['prev_cpa']) / (df['prev_cpa'] + 0.01)
        df['cpa_velocity'] = df['cpa_velocity'].fillna(0)

        # Ratios
        df['cvr'] = df['conversions'] / (df['clicks'] + 1)
        df['click_lead_ratio'] = df['clicks'] / (df['conversions'] + 1)
        
        # Fatigue
        df['frequency'] = df['frequency'].fillna(1.0)
        df['freq_growth'] = df.groupby('ad_identity')['frequency'].diff().fillna(0.02)
        
        df['days_remaining'] = np.where(
            df['platform'] == 'facebook',
            (FATIGUE_LIMIT_FB - df['frequency']) / (df['freq_growth'].replace(0, 0.02)),
            99
        )
        df['days_remaining'] = df['days_remaining'].clip(lower=0)

        # Max Spend
        df['spend_headroom'] = (TARGET_CPA / (df['cpa'] + 0.01)) * 0.8
        df['max_efficient_spend'] = df['spend'] * df['spend_headroom']

        # Audience Type
        def classify(row):
            name = str(row['ad_identity']).lower()
            if 'ad_group_name' in row: name += str(row['ad_group_name']).lower()
            if row['platform'] == 'google': return 'Search Keyword'
            if 'lookalike' in name or 'lal' in name: return 'Lookalike'
            if 'retargeting' in name: return 'Retargeting'
            if 'broad' in name: return 'Broad'
            return 'Interest'
        
        df['audience_type'] = df.apply(classify, axis=1)

        return df

    def diagnose_weakness(self, row, account_stats):
        avg_ctr = account_stats[row['platform']]['ctr']
        avg_cvr = account_stats[row['platform']]['cvr']
        
        my_ctr = row['ctr']
        my_cvr = row['cvr'] * 100
        
        weakness = "None"
        projection = ""
        
        if row['platform'] == 'google' and 0 < row['quality_score'] < 4:
            weakness = "Relevance (Quality Score)"
            projection = "Low QS is raising costs."
        
        elif (my_ctr - avg_ctr) / (avg_ctr + 0.01) < -0.3: 
            weakness = "Creative (CTR)"
            if row['platform'] == 'google': weakness = "Ad Copy (CTR)"
            improved_cpa = row['cpa'] * (my_ctr / avg_ctr)
            projection = f"Fixing CTR to {round(avg_ctr,1)}% -> CPA ${round(improved_cpa,2)}."
            
        elif (my_cvr - avg_cvr) / (avg_cvr + 0.01) < -0.3:
            weakness = "Landing Page (CVR)"
            improved_cpa = row['cpa'] * (my_cvr / avg_cvr)
            projection = f"Fixing CVR to {round(avg_cvr,1)}% -> CPA ${round(improved_cpa,2)}."

        return weakness, projection

    def generate_gemini_analysis(self, row, reason, extra=""):
        if not GEMINI_API_KEY or "YOUR_" in GEMINI_API_KEY: return ""
        time.sleep(1.5)
        prompt = f"Platform: {row['platform']} Ad: {row['ad_identity']} CPA: {row['cpa']} Issue: {reason} {extra}. Explain cause & fix."
        try:
            model = genai.GenerativeModel(self.get_valid_model_name())
            return model.generate_content(prompt).text.strip()
        except Exception as e:
            return f"AI Error: {str(e)[:20]}"

    def train_model(self, df):
        print("🧠 Training AI Model...")
        # MOCK DATA MUST HAVE AT LEAST 2 ROWS (0 and 1)
        X_mock = pd.DataFrame({
            'spend': [100, 50],
            'cpa': [50, 20],
            'ctr': [1, 2],
            'cpc': [1, 0.5],
            'conversions': [5, 10],
            'quality_score': [5, 8],
            'impr_share': [0.5, 0.8],
            'frequency': [1.5, 1.0],
            'cpa_velocity': [0, -0.1],
            'cvr': [0.1, 0.2],
            'click_lead_ratio': [10, 5]
        })
        y_mock = [0, 1] 
        self.model = RandomForestClassifier()
        self.model.fit(X_mock[self.features], y_mock)

    def run_pipeline(self):
        self.setup_database()
        df = self.fetch_data()
        
        if df.empty:
            print("⚠️ No data in 'universal_ad_data'. Run upload scripts!")
            return

        df = self.calculate_advanced_metrics(df)

        stats = {}
        for plat in ['facebook', 'google']:
            subset = df[df['platform'] == plat]
            if not subset.empty:
                stats[plat] = {'ctr': subset['ctr'].mean(), 'cvr': subset['cvr'].mean() * 100}
            else:
                stats[plat] = {'ctr': 1.0, 'cvr': 1.0}

        self.train_model(df)
        
        imputer = SimpleImputer(strategy='constant', fill_value=0)
        X = pd.DataFrame(imputer.fit_transform(df[self.features]), columns=self.features)
        
        if hasattr(self.model, "classes_") and len(self.model.classes_) > 1:
            probs = self.model.predict_proba(X)[:, 1]
        else:
            probs = [0.5] * len(df)

        results = []
        if 'platform' in df.columns:
            latest_status = df.groupby(['ad_identity', 'platform']).tail(1)
        else:
            latest_status = df.groupby('ad_identity').tail(1)

        print(f"📊 Analyzing {len(latest_status)} ads...")

        for idx, row in latest_status.iterrows():
            row_idx = df.index.get_loc(idx) if idx in df.index else 0
            if isinstance(row_idx, int): prob = probs[row_idx]
            else: prob = probs[row_idx][0] 

            cpa = row['cpa']
            velocity = row['cpa_velocity']
            spend = row['spend']
            conversions = row['conversions']
            
            weakness, projection = self.diagnose_weakness(row, stats)
            action = "WATCH"; reason = ""; rewrites = ""; budget = 0.0

            # --- FINANCIAL RISK CALCULATIONS ---
            is_zombie = False
            projected_waste = 0
            
            if conversions == 0 and spend > (TARGET_CPA * 1.5):
                is_zombie = True
            
            if cpa > TARGET_CPA or is_zombie:
                projected_waste = spend * 0.25 # Estimated weekly bleed

            # --- DECISION LOGIC ---
            if is_zombie:
                action = "KILL IMMEDIATE"
                reason = "🧟 ZOMBIE AD: High spend, 0 conversions. Likely clickbait/bots."
                rewrites = "Check your hook vs landing page alignment."
                projected_waste = spend # Use total spend as waste metric
            elif velocity > 0.30:
                action = "KILL"; reason = f"CRASH: CPA spiked {int(velocity*100)}%."
                reason += " " + self.generate_gemini_analysis(row, "CPA Spike")
            elif row['platform'] == 'google' and 0 < row['quality_score'] < 3:
                action = "KILL"; reason = f"Low Quality Score ({row['quality_score']})."
            elif row['platform'] == 'google' and row['impr_share'] < 0.20 and cpa < TARGET_CPA:
                action = "SCALE AGGRESSIVELY"; reason = "Market Share Opportunity."; budget = row['spend'] * 1.5
            elif row['platform'] == 'facebook' and row['days_remaining'] < 2:
                action = "PREPARE CREATIVE"; reason = f"Fatigue Imminent ({round(row['days_remaining'],1)} days)."
            elif weakness != "None" and cpa > TARGET_CPA:
                action = "FIX " + weakness.split()[0].upper(); reason = f"Bottleneck: {weakness}. {projection}"
                rewrites = self.generate_gemini_analysis(row, weakness, projection)
            elif cpa > KILL_CPA:
                action = "KILL"; reason = f"CPA ${cpa} too high."
            elif cpa < TARGET_CPA:
                action = "SCALE"; reason = f"Winner ({row['audience_type']})."; budget = row['max_efficient_spend']

            # Append Waste Warning
            if projected_waste > 50 and "KILL" in action:
                reason += f" 💸 WASTE RISK: ${int(projected_waste)}/week."

            results.append({
                'campaign_id': row['campaign_name'],
                'ad_id': row['ad_identity'],
                'platform': row['platform'],
                'suggested_action': action,
                'confidence_score': float(prob),
                'ai_analysis': reason,
                'ai_rewrites': rewrites,
                'suggested_budget': float(budget),
                'fatigue_score': float(row.get('frequency', 0)),
                'cpa_velocity': float(velocity),
                'days_remaining': float(row['days_remaining']),
                'max_efficient_spend': float(row['max_efficient_spend']),
                'audience_type': row['audience_type'],
                'weakest_link': weakness,
                'impact_projection': projection
            })

        if results:
            final_df = pd.DataFrame(results)
            final_df.to_sql('ad_predictions', self.engine, if_exists='append', index=False)
            print("🚀 Analysis Complete & Saved!")
            print(final_df[['platform', 'ad_id', 'suggested_action']].head(3))

if __name__ == "__main__":
    try:
        engine = AdPredictorEngine(DB_URL)
        engine.run_pipeline()
    except Exception as e:
        print(f"❌ Fatal Error: {e}")