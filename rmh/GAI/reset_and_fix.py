import urllib.parse
from sqlalchemy import create_engine, text

# ==========================================
# CONFIGURATION
# ==========================================
DB_USER = "neondb_owner" 
DB_PASS = "npg_kvbAhwHVu15g"
DB_HOST = "ep-restless-bird-ahug88k0-pooler.c-3.us-east-1.aws.neon.tech"
DB_NAME = "neondb"
ENDPOINT_ID = "ep-restless-bird-ahug88k0-pooler"

encoded_pass = urllib.parse.quote_plus(DB_PASS)
DB_URL = (
    f"postgresql://{DB_USER}:{encoded_pass}@{DB_HOST}:5432/{DB_NAME}"
    f"?sslmode=require&options=endpoint%3D{ENDPOINT_ID}"
)

def fix_database():
    engine = create_engine(DB_URL)
    print("⏳ Deleting broken table...")
    
    with engine.begin() as conn:
        # 1. DROP the old table that is missing columns
        conn.execute(text("DROP TABLE IF EXISTS universal_ad_data CASCADE;"))
        
        # 2. CREATE the new table with 'conversions' explicitly defined
        conn.execute(text("""
            CREATE TABLE universal_ad_data (
                id SERIAL PRIMARY KEY,
                platform TEXT,
                campaign_name TEXT,
                ad_group_name TEXT,
                ad_identity TEXT,
                spend NUMERIC,
                impressions INT,
                clicks INT,
                conversions NUMERIC DEFAULT 0,  -- <--- The missing column
                cpa NUMERIC,
                ctr NUMERIC,
                cpc NUMERIC,
                quality_score INT DEFAULT 0,
                impr_share NUMERIC DEFAULT 0,
                frequency NUMERIC DEFAULT 1,
                reporting_date DATE
            );
        """))
        
    print("✅ Table Fixed! The 'conversions' column now exists.")
    print("👉 NEXT STEPS:")
    print("1. Run upload_facebook.py")
    print("2. Run upload_google.py")
    print("3. Run ad_brain.py")

if __name__ == "__main__":
    fix_database()