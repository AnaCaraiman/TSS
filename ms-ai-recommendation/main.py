from fastapi import FastAPI
from pydantic import BaseModel
from most_popular_recommender import MostPopularRecommender
from personalised_recommender import PersonalizedRecommender

app = FastAPI()

recommender = MostPopularRecommender()
personalized_recommender = PersonalizedRecommender()


class RecommendationRequest(BaseModel):
    user_id: int
    category: str | None = None


@app.post("/api/recommend")
def recommend(request: RecommendationRequest):
    recommendations = recommender.recommend(top_n=5, category_filter=request.category)
    return recommendations


@app.post("/api/recommend/personalised")
def recommend_personalized(request: RecommendationRequest):
    try:
        return personalized_recommender.recommend_for_user(user_id=request.user_id, top_n=5)
    except Exception as e:
        return {"error": str(e)}


@app.post("/api/retrain")
def retrain_model():
    try:
        recommender._build_popularity_scores()
        personalized_recommender.train_model()
        return {"status": "Models retrained"}
    except Exception as e:
        return {"error": str(e)}
