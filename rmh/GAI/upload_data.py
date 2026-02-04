import pandas as pd
import glob
import os
import urllib.parse
from sqlalchemy import create_engine, text

# ==========================================
# 1. DATABASE CONFIGURATION
# ==========================================
DB_USER = "neondb_owner" 
DB_PASS = "npg_kvbAhwHVu15g"  # <--- PASTE PASSWORD
DB_HOST = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech"
DB_NAME = "neondb"
ENDPOINT_ID = "ep-restless-bird-ahug88k0-pooler" # <--- Ensures connection works

# Construct Secure URL
encoded_pass = urllib.parse.quote_plus(DB_PASS)
DB_URL = (
    f"postgresql://{DB_USER}:{encoded_pass}@{DB_HOST}:5432/{DB_NAME}"
    f"?sslmode=require&options=endpoint%3D{ENDPOINT_ID}"
)

def bulk_upload_csvs():
    # 2. Connect to Database
    try:
        engine = create_engine(DB_URL)
        print("✅ Connected to Database.")
    except Exception as e:
        print(f"❌ Connection Failed: {e}")
        return

    # 3. Find all CSV files in the current folder
    csv_files = glob.glob("*.csv")
    print(f"📂 Found {len(csv_files)} CSV files: {csv_files}")

    if not csv_files:
        print("No CSV files found.")
        return

    all_data = []

    # 4. Loop through files and merge them
    for filename in csv_files:
        print(f"   Reading {filename}...")
        try:
            df = pd.read_csv(filename)
            
            # Standardization: Rename columns to match database schema
            df.columns = [
                'campaign_name', 'ad_set_name', 'delivery_status', 'delivery_level', 
                'reach', 'impressions', 'frequency', 'result_type', 'results', 
                'cost_per_result', 'amount_spent_usd', 'cpm', 'link_clicks', 
                'cpc', 'ctr', 'reporting_starts', 'reporting_ends'
            ]
            
            all_data.append(df)
        except Exception as e:
            print(f"⚠️ Skipped {filename} due to error: {e}")

    # 5. Upload Strategy: TRUNCATE + APPEND
    if all_data:
        final_df = pd.concat(all_data, ignore_index=True)
        print(f"📊 Processing {len(final_df)} total rows...")

        with engine.begin() as conn:
            print("🧹 Clearing old data (Truncate)...")
            # This wipes the data but KEEPS the table and view structure safe
            conn.execute(text("TRUNCATE TABLE campaign_data RESTART IDENTITY CASCADE;"))
        
        print("💾 Uploading new data...")
        # We use 'append' because the table is now empty but exists
        final_df.to_sql('campaign_data', engine, if_exists='append', index=False)
        
        print("🚀 Success! Data updated.")
    else:
        print("No data to upload.")

if __name__ == "__main__":
    bulk_upload_csvs()