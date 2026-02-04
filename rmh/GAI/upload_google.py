import pandas as pd
import glob
import os
import urllib.parse
import csv
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

def clean_currency(x):
    if pd.isna(x) or str(x).strip() == '--': return 0.0
    clean_str = str(x).replace('$', '').replace(',', '').replace('%', '')
    try: return float(clean_str)
    except: return 0.0

def clean_percentage(x):
    if pd.isna(x) or str(x).strip() == '--' or '<' in str(x): return 0.0
    clean_str = str(x).replace('%', '').replace(',', '')
    try: return float(clean_str) / 100.0
    except: return 0.0

def find_header_row(filepath):
    """
    Scans the file line-by-line to find the row number containing headers.
    Returns: (row_number, encoding)
    """
    encodings = ['utf-8', 'utf-16', 'latin-1'] # Google sometimes uses utf-16
    
    for enc in encodings:
        try:
            with open(filepath, 'r', encoding=enc) as f:
                for i, line in enumerate(f):
                    # Check for key columns that MUST exist
                    line_lower = line.lower()
                    if 'campaign' in line_lower and 'cost' in line_lower:
                        return i, enc
            # If we read the whole file and found nothing, try next encoding
        except UnicodeError:
            continue # Wrong encoding, try next
            
    return None, None

def upload_google_ads():
    engine = create_engine(DB_URL)
    folder_path = "google"
    
    if not os.path.exists(folder_path):
        os.makedirs(folder_path)
        print(f"⚠️ Created folder: {folder_path}/")
        return

    csv_files = glob.glob(os.path.join(folder_path, "*.csv"))
    print(f"📂 Scanning '{folder_path}/'...")
    
    all_data = []
    for filename in csv_files:
        print(f"   Processing {filename}...")
        
        # 1. FIND THE HEADER ROW SAFELY
        skip_count, detected_encoding = find_header_row(filename)
        
        if skip_count is None:
            print(f"⚠️ Skipping {filename}: Could not find header row (must contain 'Campaign' and 'Cost').")
            continue
            
        print(f"      ✅ Header found at row {skip_count} (Encoding: {detected_encoding})")

        try:
            # 2. LOAD DATA
            df = pd.read_csv(filename, skiprows=skip_count, encoding=detected_encoding)
            
            # Normalize Headers (Strip spaces)
            df.columns = df.columns.str.strip()
            
            # Map to Universal Schema
            universal_df = pd.DataFrame()
            universal_df['platform'] = ['google'] * len(df)
            
            # Flexible Column Matching
            if 'Campaign' in df.columns: universal_df['campaign_name'] = df['Campaign']
            elif 'Campaign state' in df.columns: universal_df['campaign_name'] = "Unknown" # Fallback
            
            if 'Ad group' in df.columns: universal_df['ad_group_name'] = df['Ad group']
            else: universal_df['ad_group_name'] = "General"
            
            # Identity (Keyword or Search Term)
            if 'Keyword' in df.columns: universal_df['ad_identity'] = df['Keyword']
            elif 'Search term' in df.columns: universal_df['ad_identity'] = df['Search term']
            else: universal_df['ad_identity'] = "Unknown Keyword"

            # Metrics
            cols = df.columns
            universal_df['spend'] = df['Cost'].apply(clean_currency) if 'Cost' in cols else 0
            universal_df['impressions'] = df['Impr.'].apply(clean_currency) if 'Impr.' in cols else 0
            universal_df['clicks'] = df['Clicks'].apply(clean_currency) if 'Clicks' in cols else 0
            universal_df['conversions'] = df['Conversions'].apply(clean_currency) if 'Conversions' in cols else 0
            
            if 'Cost / conv.' in cols: universal_df['cpa'] = df['Cost / conv.'].apply(clean_currency)
            else: universal_df['cpa'] = 0

            if 'CTR' in cols: universal_df['ctr'] = df['CTR'].apply(clean_percentage) * 100
            else: universal_df['ctr'] = 0
            
            # Google Specifics
            if 'Quality score' in cols:
                universal_df['quality_score'] = df['Quality score'].replace('--', 0).astype(float)
            else:
                universal_df['quality_score'] = 0

            if 'Search Impr. share' in cols:
                universal_df['impr_share'] = df['Search Impr. share'].apply(clean_percentage)
            else:
                universal_df['impr_share'] = 0
            
            universal_df['frequency'] = 1.0 
            universal_df['reporting_date'] = pd.Timestamp.now().date()
            
            all_data.append(universal_df)

        except Exception as e:
            print(f"⚠️ Error processing data in {filename}: {e}")

    if all_data:
        final_df = pd.concat(all_data)
        final_df.to_sql('universal_ad_data', engine, if_exists='append', index=False)
        print("🚀 Google Data Uploaded Successfully!")
    else:
        print("No valid data found to upload.")

if __name__ == "__main__":
    upload_google_ads()