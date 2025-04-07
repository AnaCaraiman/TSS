<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Transformers\ProductTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function createProduct(Request $request): JsonResponse
    {
        try{
            $data = $request->all();
            $validData = ProductTransformer::transform($data);
            $product = $this->productService->createProduct($validData);

            return response()->json([
                'message' => 'Product created successfully',
                'product' => $product
            ],201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function getProducts(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Products retrieved successfully',
            'products' => $this->productService->getProducts()
        ],201);
    }

    public function getProduct(Request $request): JsonResponse
    {
        try {
            $id = (int)$request->route('id');
            $product = $this->productService->getProduct($id);

            return response()->json([
                'product' => $product
            ], 201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function removeProduct(Request $request): JsonResponse
    {
        try{
            $id = $request->route('id');
            $this->productService->removeProduct($id);

            return response()->json([
                    'message' => 'Product deleted successfully'
                ],201);
        }
        catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

}
