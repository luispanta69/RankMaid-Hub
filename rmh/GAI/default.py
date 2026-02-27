import google.generativeai as genai
import os
import json

# Configure your API Key
genai.configure(api_key="YOUR_GOOGLE_API_KEY")

def analyze_creative_with_gemini(ad_image_url, ad_headline, ad_body):
    """
    Sends ad assets to Gemini 1.5 Pro to get a qualitative score.
    """
    
    # 1. Define the Persona and Task
    prompt = f"""
    You are an expert Facebook Ads Media Buyer. 
    Analyze the following ad creative elements:
    
    Headline: "{ad_headline}"
    Body Copy: "{ad_body}"
    Image URL: {ad_image_url} (Treat this as the visual input)

    Task:
    1. Rate the "Hook" (1-10): Is it attention-grabbing?
    2. Rate the "Clarity" (1-10): Is the offer clear?
    3. Identify the "Emotion": (e.g., Fear, Joy, FOMO).
    4. Provide a 1-sentence critique of why it might fail.

    Return the result strictly as JSON:
    {{
        "hook_score": int,
        "clarity_score": int,
        "dominant_emotion": string,
        "critique": string
    }}
    """

    # 2. Call the Model
    model = genai.GenerativeModel('gemini-1.5-pro')
    
    # In a real scenario, you would download the image bytes first or use the file API
    # For this example, we assume we are passing text description or using the Vision capabilities
    response = model.generate_content([prompt]) 
    
    # 3. Parse JSON
    try:
        # Clean up response to ensure valid JSON
        result_text = response.text.replace('```json', '').replace('```', '')
        return json.loads(result_text)
    except:
        return {"hook_score": 5, "clarity_score": 5, "critique": "Analysis failed"}