# app/main.py
from fastapi import FastAPI, HTTPException
from app.build_dataset import build_dataset
from app.model import load_and_train_model, recommend  # assume your model.py exposes these
from app.schemas import RecommendationRequest

app = FastAPI(title="Product Recommendation API")


@app.on_event("startup")
def startup_event():
    # if anything here fails, uvicorn will log the traceback and exit
    csv_path = build_dataset()
    load_and_train_model(csv_path)
    print("🚀 Startup complete: dataset built and model trained.")


@app.post("/train")
def retrain():
    csv_path = build_dataset()
    result = load_and_train_model(csv_path)
    return {"status": "retrained", **result}


@app.get("/recommend")
def recommend_products(req: RecommendationRequest):
    try:
        recs = recommend(req.product_id, req.top_n)
        return {"recommendations": recs}
    except ValueError as e:
        raise HTTPException(status_code=404, detail=str(e))
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
