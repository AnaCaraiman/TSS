# app/build_dataset.py
import os
import pandas as pd
import requests

def build_dataset(output_path: str = None) -> str:
    """
    Fetch products from the PRODUCTS_API, build an enriched CSV,
    and return the path to the saved file.

    Raises ValueError if no products were written.
    """
    API = "http://ms-product-nginx:81/api/ms-product"
    print("⏳ Fetching products…")
    resp = requests.get(API)
    resp.raise_for_status()

    data = resp.json()
    # support both {"products": [...]} and top-level list
    products = data.get("products") if isinstance(data, dict) else data
    if products is None:
        raise ValueError(f"No 'products' key in JSON and top-level was not a list.")

    records = []
    for p in products:
        attrs = p.get("attributes", [])
        txt = " ".join(f"{a['attribute_name']} {a['attribute_value']}" for a in attrs)
        records.append({
            "product_id":     p["id"],
            "name":           p["name"],
            "brand":          p["brand"],
            "category":       p.get("category", {}).get("name", ""),
            "description":    p["description"],
            "price":          p["price"],
            "stock_quantity": p["stock_quantity"],
            "attributes_text": txt,
        })

    df = pd.DataFrame(records)
    # fail early if empty
    if df.empty:
        raise ValueError("Fetched product list was empty — no rows to write!")

    # build the search text
    df["text"] = (
        df["name"] + " " + df["brand"] + " " +
        df["category"] + " " + df["description"] + " " +
        df["attributes_text"]
    ).str.lower() \
     .str.replace(r"[^a-z0-9\s]", " ", regex=True) \
     .str.replace(r"\s+", " ", regex=True) \
     .str.strip()

    # where to write?
    if output_path is None:
        # place alongside this module
        base = os.path.dirname(os.path.abspath(__file__))
        output_path = os.path.join(base, "..", "products.csv")

    print("DEBUG: cwd is", os.getcwd())
    print("DEBUG: about to write CSV with", len(df), "rows to", os.path.abspath(output_path))
    df.to_csv(output_path, index=False)
    print(f"✅ Written {len(df)} products to {output_path}")
    return output_path
