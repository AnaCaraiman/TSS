import pandas as pd
import requests


class MostPopularRecommender:
    def __init__(self):
        self.interaction_url = "http://ms-recommendation-nginx:87/api/recommendation"
        self.product_url = "http://ms-product-nginx:81/api/ms-product"
        self.category_map = {}  # To optionally recommend per category
        self.popularity_scores = {}
        self.weights = {
            1: 1,  # View
            2: 3,  # Add to Favorites
            3: 5,  # Add to Cart
            4: 10,  # Purchase
        }
        self._load_products()
        self._build_popularity_scores()

    def _load_products(self):
        """Fetch products and create category map."""
        products = requests.get(self.product_url).json().get("products", [])
        for product in products:
            self.category_map[product["id"]] = product.get("category", {}).get("name", "Unknown")

    def _build_popularity_scores(self):
        interactions = requests.get(self.interaction_url).json().get("actions", [])
        df = pd.DataFrame(interactions)

        if df.empty:
            return

        df["score"] = df["action_id"].map(self.weights).fillna(0)
        scores = df.groupby("product_id")["score"].sum()
        self.popularity_scores = scores.sort_values(ascending=False).to_dict()

    def recommend(self, top_n=5, category_filter=None):
        if category_filter:
            filtered = {
                pid: score
                for pid, score in self.popularity_scores.items()
                if self.category_map.get(pid) == category_filter
            }
            return list(filtered.keys())[:top_n]
        return list(self.popularity_scores.keys())[:top_n]
