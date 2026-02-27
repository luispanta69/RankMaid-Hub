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
            df.columns = df.columns.str.strip().str.lower()
            
            # Map to Universal Schema
            universal_df = pd.DataFrame()
            universal_df['platform'] = ['google'] * len(df)
            
            # Flexible Column Matching
            if 'Campaign' in df.columns: universal_df['campaign_name'] = df['Campaign']
            elif 'Campaign state' in df.columns: universal_df['campaign_name'] = "Unknown" # Fallback
            # Campaign Name
            campaign_col = next((col for col in df.columns if 'campaign' in col), None)
            if campaign_col: universal_df['campaign_name'] = df[campaign_col]
            else: universal_df['campaign_name'] = "Unknown Campaign" # Fallback
            
            if 'Ad group' in df.columns: universal_df['ad_group_name'] = df['Ad group']
            # Ad Group Name
            ad_group_col = next((col for col in df.columns if 'ad group' in col), None)
            if ad_group_col: universal_df['ad_group_name'] = df[ad_group_col]
            else: universal_df['ad_group_name'] = "General"
            
            # Identity (Keyword or Search Term)
            if 'Keyword' in df.columns: universal_df['ad_identity'] = df['Keyword']
            elif 'Search term' in df.columns: universal_df['ad_identity'] = df['Search term']
            keyword_col = next((col for col in df.columns if 'keyword' in col), None)
            search_term_col = next((col for col in df.columns if 'search term' in col), None)
            if keyword_col: universal_df['ad_identity'] = df[keyword_col]
            elif search_term_col: universal_df['ad_identity'] = df[search_term_col]
            else: universal_df['ad_identity'] = "Unknown Keyword"

            # Metrics
            cols = df.columns
            universal_df['spend'] = df['Cost'].apply(clean_currency) if 'Cost' in cols else 0
            universal_df['impressions'] = df['Impr.'].apply(clean_currency) if 'Impr.' in cols else 0
            universal_df['clicks'] = df['Clicks'].apply(clean_currency) if 'Clicks' in cols else 0
            universal_df['conversions'] = df['Conversions'].apply(clean_currency) if 'Conversions' in cols else 0
            
            if 'Cost / conv.' in cols: universal_df['cpa'] = df['Cost / conv.'].apply(clean_currency)
            else: universal_df['cpa'] = 0
            spend_col = next((col for col in cols if 'cost' in col), None)
            universal_df['spend'] = df[spend_col].apply(clean_currency) if spend_col else 0.0
            
            impr_col = next((col for col in cols if 'impr.' in col or 'impressions' in col), None)
            universal_df['impressions'] = df[impr_col].apply(clean_currency).astype(int) if impr_col else 0
            
            clicks_col = next((col for col in cols if 'clicks' in col), None)
            universal_df['clicks'] = df[clicks_col].apply(clean_currency).astype(int) if clicks_col else 0
            
            conversions_col = next((col for col in cols if 'conversions' in col), None)
            universal_df['conversions'] = df[conversions_col].apply(clean_currency) if conversions_col else 0.0
            
            cpa_col = next((col for col in cols if 'cost / conv.' in col or 'cpa' in col), None)
            universal_df['cpa'] = df[cpa_col].apply(clean_currency) if cpa_col else 0.0

            ctr_col = next((col for col in cols if 'ctr' in col), None)
            universal_df['ctr'] = df[ctr_col].apply(clean_percentage) * 100 if ctr_col else 0.0
            
            # Calculate CPC (Cost Per Click)
            universal_df['cpc'] = universal_df.apply(lambda row: row['spend'] / row['clicks'] if row['clicks'] > 0 else 0.0, axis=1)
            
            # Google Specifics
            quality_score_col = next((col for col in cols if 'quality score' in col), None)
            universal_df['quality_score'] = df[quality_score_col].replace('--', 0).astype(float).astype(int) if quality_score_col else 0

            impr_share_col = next((col for col in cols if 'impr. share' in col or 'impression share' in col), None)
            universal_df['impr_share'] = df[impr_share_col].apply(clean_percentage) if impr_share_col else 0.0
            
            universal_df['frequency'] = 1.0 # Google Ads often doesn't provide direct frequency per ad, defaulting to 1.0
            
            # Reporting Date: Look for 'day', 'date', or 'reporting starts'
            date_col = next((col for col in cols if 'day' in col or 'date' in col or 'reporting starts' in col), None)
            if date_col:
                # Use errors='coerce' to turn unparseable dates into NaT
                universal_df['reporting_date'] = pd.to_datetime(df[date_col], errors='coerce').dt.date
            else:
                print(f"      ⚠️ Warning: No 'Day' or 'Date' column found in {filename}. Using current date for all records.")
                universal_df['reporting_date'] = pd.Timestamp.now().date()
            
            # Drop rows where reporting_date is NaT (e.g., summary rows with " --" in date column)
            universal_df.dropna(subset=['reporting_date'], inplace=True)
            
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