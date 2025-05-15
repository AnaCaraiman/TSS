import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.neighbors import NearestNeighbors

df = None
tfidf = None
tfidf_matrix = None
nn = None


def load_and_train_model(csv_path: str = "data/products.csv"):
    global df, tfidf, tfidf_matrix, nn

    df = pd.read_csv(csv_path)

    if 'text' not in df.columns or 'product_id' not in df.columns:
        raise ValueError("CSV must contain 'product_id' and 'text' columns.")

    tfidf = TfidfVectorizer(stop_words='english', max_features=5000)
    tfidf_matrix = tfidf.fit_transform(df['text'])

    nn = NearestNeighbors(metric='cosine', algorithm='brute')
    nn.fit(tfidf_matrix)

    return {"message": "Model trained successfully", "num_products": len(df)}


def recommend(product_id: int, top_n: int = 5):
    global df, tfidf_matrix, nn

    if df is None:
        raise RuntimeError("Dataframe not loaded. Please train the model first.")
    if tfidf_matrix is None or nn is None:
        raise RuntimeError("TF-IDF or NearestNeighbors model not initialized.")

    if product_id not in df['product_id'].values:
        raise ValueError(f"Product ID {product_id} not found.")

    try:
        idx = df.index[df['product_id'] == product_id][0]
        distances, indices = nn.kneighbors(tfidf_matrix[idx], n_neighbors=top_n + 1)
        recs = indices[0][1:top_n + 1]
        return df.iloc[recs][['product_id', 'name', 'brand', 'price']].to_dict(orient='records')
    except Exception as e:
        raise RuntimeError(f"Failed to generate recommendations: {e}")
