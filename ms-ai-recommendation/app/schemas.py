from pydantic import BaseModel


class RecommendationRequest(BaseModel):
    product_id: int
    top_n: int = 5
