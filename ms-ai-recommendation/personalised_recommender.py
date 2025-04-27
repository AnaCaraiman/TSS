import pandas as pd
import requests
from scipy.sparse import coo_matrix
from implicit.als import AlternatingLeastSquares


class PersonalizedRecommender:
    def __init__(self):
        self.interaction_url = "http://ms-recommendation-nginx:87/api/recommendation"
        self.user_url = "http://ms-auth-nginx:79/api/auth/users"
        self.product_url = "http://ms-product-nginx:81/api/ms-product"

        self.model = None
        self.user_id_map = {}
        self.product_id_map = {}
        self.inverse_user_map = {}
        self.inverse_product_map = {}
        self.user_item_matrix = None
        self.train_model()

    def fetch_data(self):
        interactions = requests.get(self.interaction_url).json().get("actions", [])
        return pd.DataFrame(interactions)

    def preprocess(self, df):
        weights = {
            1: 1,  # View
            2: 3,  # Favorite
            3: 5,  # Add to Cart
            4: 10  # Purchase
        }
        df = df.copy()
        df["score"] = df["action_id"].map(weights).fillna(0)

        print("Score column after mapping:")
        print(df["score"].describe())

        df["user_id"] = df["user_id"].astype(int)
        df["product_id"] = df["product_id"].astype(int)

        # 🛠 Filter invalid or low score rows (optional but good)
        df = df[df["score"] > 0]

        # 🔁 Map *after* filtering
        user_ids = df["user_id"].unique()
        product_ids = df["product_id"].unique()

        self.user_id_map = {int(uid): idx for idx, uid in enumerate(user_ids)}
        self.product_id_map = {int(pid): idx for idx, pid in enumerate(product_ids)}
        self.inverse_user_map = {idx: uid for uid, idx in self.user_id_map.items()}
        self.inverse_product_map = {idx: pid for pid, idx in self.product_id_map.items()}

        print("Mapped user_ids:", self.user_id_map)
        print("Mapped product_ids:", self.product_id_map)

        df = df[df["user_id"].isin(self.user_id_map) & df["product_id"].isin(self.product_id_map)]
        df["user_idx"] = df["user_id"].map(self.user_id_map)
        df["product_idx"] = df["product_id"].map(self.product_id_map)

        matrix = coo_matrix(
            (df["score"], (df["user_idx"], df["product_idx"])),
            shape=(len(self.user_id_map), len(self.product_id_map))
        )

        print("Matrix shape:", matrix.shape)

        return matrix

    def train_model(self):
        df = self.fetch_data()
        if df.empty:
            print("❌ No interaction data available.")
            return

        print("✅ RAW INTERACTIONS:")
        print(df.head())

        matrix = self.preprocess(df)

        if matrix.shape[0] == 0 or matrix.shape[1] == 0:
            print("❌ Empty matrix generated. Not enough user/product interactions.")
            return

        self.user_item_matrix = matrix

        self.model = AlternatingLeastSquares(factors=50, regularization=0.01, iterations=15)
        self.model.fit(matrix.T)  # implicit library expects item-user matrix

        print("✅ Model trained.")
        print("User ID map:", self.user_id_map)
        print("Product ID map:", self.product_id_map)

        print("🔥 TESTING RECOMMENDATIONS FOR ALL USERS")
        for user_id in self.user_id_map.keys():
            recs = self.recommend_for_user(user_id)
            print(f"User {user_id} => {recs}")

    def recommend_for_user(self, user_id, top_n=5):
        print("User map:", self.user_id_map)
        if user_id not in self.user_id_map:
            print(f"❌ User {user_id} not found in user_id_map.")
            return []

        user_idx = self.user_id_map[user_id]
        user_vector = self.model.user_factors[user_idx]
        print(f"User {user_id} factor vector: {user_vector}")

        recommendations = self.model.recommend(user_idx, self.user_item_matrix, N=top_n,
                                               filter_already_liked_items=False)

        product_indices = []
        for rec in recommendations:
            try:
                if isinstance(rec, (list, tuple)) and len(rec) == 2:
                    item_id, _ = rec
                elif hasattr(rec, "shape") and rec.shape == (2,):  # numpy array of 2 elements
                    item_id = rec[0]
                else:
                    print(f"⚠️ Skipping invalid recommendation item: {rec}")
                    continue

                if hasattr(item_id, 'item'):
                    item_id = item_id.item()

                product_indices.append(self.inverse_product_map[int(item_id)])
            except Exception as e:
                print(f"⚠️ Skipping invalid recommendation item: {rec} — {e}")
                continue

        return product_indices


