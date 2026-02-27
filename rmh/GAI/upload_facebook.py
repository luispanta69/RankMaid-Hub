import pandas as pd
import glob
import os
import urllib.parse
from sqlalchemy import create_engine

# ==========================================
# CONFIGURATION
# ==========================================
DB_USER = "neondb_owner" 
DB_PASS = "npg_kvbAhwHVu15g" # <--- PASTE PASSWORD
DB_HOST = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech"
DB_NAME = "neondb"
ENDPOINT_ID = "ep-restless-bird-ahug88k0-pooler"

encoded_pass = urllib.parse.quote_plus(DB_PASS)
DB_URL = (
    f"postgresql://{DB_USER}:{encoded_pass}@{DB_HOST}:5432/{DB_NAME}"
    f"?sslmode=require&options=endpoint%3D{ENDPOINT_ID}"
)

def find_header_row_facebook(filepath):
    """
    Scans the file line-by-line to find the row number containing headers for Facebook CSVs.
    Returns: (row_number, encoding)
    """
    encodings = ['utf-8', 'utf-16', 'latin-1', 'cp1252', 'utf-8-sig'] # Common encodings
    
    for enc in encodings:
        try:
            with open(filepath, 'r', encoding=enc) as f:
                for i, line in enumerate(f):
                    line_lower = line.lower()
                    if ('campaign name' in line_lower or 'campaign' in line_lower) and \
                       ('amount spent (usd)' in line_lower or 'amount spent' in line_lower or 'spend' in line_lower):
                        return i, enc
        except UnicodeError:
            continue # Wrong encoding, try next
            
    return None, None

def upload_facebook_ads():
    engine = create_engine(DB_URL)
    
    # LOOK IN 'facebook' FOLDER
    folder_path = "facebook"
    if not os.path.exists(folder_path):
        os.makedirs(folder_path) # Create if missing
        print(f"⚠️ Created missing folder: {folder_path}/. Please put CSVs here.")
        return

    csv_files = glob.glob(os.path.join(folder_path, "*.csv"))
    
    print(f"📂 Scanning '{folder_path}/'...")
    print(f"   Found {len(csv_files)} CSV files.")
    
    all_data = []
    for filename in csv_files:
        print(f"   Processing {filename}...")
        
        # 1. FIND THE HEADER ROW SAFELY
        skip_count, detected_encoding = find_header_row_facebook(filename)
        
        if skip_count is None:
            print(f"      ⚠️ Skipping {filename}: Could not find header row (must contain 'Campaign name' and 'Amount spent (USD)').")
            continue
            
        print(f"      ✅ Header found at row {skip_count} (Encoding: {detected_encoding})")

        try:
            # 2. LOAD DATA
            df = pd.read_csv(filename, skiprows=skip_count, encoding=detected_encoding)
            
            # Map to Universal Schema
            universal_df = pd.DataFrame()
            universal_df['platform'] = ['facebook'] * len(df)
            
            # FB Column Mapping
            universal_df['campaign_name'] = df['Campaign name']
            universal_df['ad_group_name'] = df['Ad set name']
            universal_df['ad_identity'] = df['Ad set name'] # Using Ad Set as ID
            
            universal_df['spend'] = df['Amount spent (USD)']
            universal_df['impressions'] = df['Impressions']
            universal_df['clicks'] = df['Link clicks']
            universal_df['conversions'] = df['Results']
            universal_df['cpa'] = df['Cost per result']
            universal_df['ctr'] = df['CTR (link click-through rate)']
            universal_df['cpc'] = df['CPC (cost per link click)']
            universal_df['frequency'] = df['Frequency']
            
            # Fill Defaults
            universal_df['quality_score'] = 0
            universal_df['impr_share'] = 0
            universal_df['reporting_date'] = pd.to_datetime(df['Reporting starts']).dt.date
            
            all_data.append(universal_df)
        except Exception as e:
            print(f"⚠️ Error reading {filename}: {e}")

    if all_data:
        final_df = pd.concat(all_data)
        final_df.to_sql('universal_ad_data', engine, if_exists='append', index=False)
        print("🚀 Facebook Data Uploaded!")
    else:
        print("No CSVs found in /facebook folder.")

if __name__ == "__main__":
    upload_facebook_ads()