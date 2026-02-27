import urllib.parse
from sqlalchemy import create_engine, text

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

def reset_database():
    engine = create_engine(DB_URL)
    print("⏳ Resetting 'universal_ad_data' table...")
    
    with engine.begin() as conn:
        # 1. DROP the broken table
        conn.execute(text("DROP TABLE IF EXISTS universal_ad_data CASCADE;"))
        
        # 2. CREATE the correct table
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
                conversions NUMERIC DEFAULT 0,  -- <--- This is the missing column
                cpa NUMERIC,
                ctr NUMERIC,
                cpc NUMERIC,
                quality_score INT DEFAULT 0,
                impr_share NUMERIC DEFAULT 0,
                frequency NUMERIC DEFAULT 1,
                reporting_date DATE
            );
        """))
        
    print("Table Reset! Now run your upload scripts.")

if __name__ == "__main__":
    reset_database()