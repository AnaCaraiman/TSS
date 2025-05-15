from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.neighbors import NearestNeighbors

app = FastAPI(title="Product Recommendation API")

df = None
tfidf = None
tfidf_matrix = None
nn = None


@app.on_event("startup")
def startup_event():
    global df, tfidf, tfidf_matrix, nn
    try:
        df = pd.read_csv('products.csv')
        if 'text' not in df.columns or 'product_id' not in df.columns:
            raise ValueError("CSV must contain 'product_id' and 'text' columns.")

        tfidf = TfidfVectorizer(stop_words='english', max_features=5000)
        tfidf_matrix = tfidf.fit_transform(df['text'])
        nn = NearestNeighbors(metric='cosine', algorithm='brute')
        nn.fit(tfidf_matrix)

        print(f"Model trained on startup with {len(df)} products.")

    except Exception as e:
        print(f"Startup model training failed: {e}")


class RecommendationRequest(BaseModel):
    product_id: int
    top_n: int = 5


@app.post("/train/")
def train_model():
    global df, tfidf, tfidf_matrix, nn

    try:
        df = pd.read_csv('products.csv')
    except Exception as e:
        raise HTTPException(status_code=400, detail=f"Failed to read CSV: {str(e)}")

    if 'text' not in df.columns or 'product_id' not in df.columns:
        raise HTTPException(status_code=422, detail="CSV must contain 'product_id' and 'text' columns.")

    tfidf = TfidfVectorizer(stop_words='english', max_features=5000)
    tfidf_matrix = tfidf.fit_transform(df['text'])
    nn = NearestNeighbors(metric='cosine', algorithm='brute')
    nn.fit(tfidf_matrix)

    return {"message": "Model trained successfully", "num_products": len(df)}


@app.post("/recommend/")
def recommend_products(req: RecommendationRequest):
    global df, tfidf_matrix, nn

    if df is None or nn is None:
        raise HTTPException(status_code=400, detail="Model not trained. Please call /train first.")

    if req.product_id not in df['product_id'].values:
        raise HTTPException(status_code=404, detail="Product ID not found")

    idx = df.index[df['product_id'] == req.product_id][0]
    distances, indices = nn.kneighbors(tfidf_matrix[idx], n_neighbors=req.top_n + 1)
    recs = indices[0][1:req.top_n + 1]
    results = df.iloc[recs][['product_id', 'name', 'brand', 'price']].to_dict(orient='records')

    return {"recommendations": results}
