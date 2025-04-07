<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class ProductController
{
    private string $productServiceUrl;

    public function __construct()
    {
        $this->productServiceUrl = config('services.ms_product.url');
    }

    public function getProduct(Request $request): JsonResponse
    {
        try {
            $id = $request->route('id');
            $response = Http::get($this->productServiceUrl . '/api/ms-product/' . $id, []);
            return response()->json(
                json_decode($response->getBody()->getContents(), true)
            );
        }
        catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
                ],400);
        }
    }
    //,
    public function getProducts(): JsonResponse
    {
        $response = Http::get($this->productServiceUrl . '/api/ms-product',[]);
        return response()->json(
            json_decode($response->getBody()->getContents(), true)
        );
    }

    public function getCategories(): JsonResponse {
        $response = Http::get($this->productServiceUrl . '/api/ms-category',[]);
        return response()->json(
            json_decode($response->getBody()->getContents(), true)
        );
    }





}
