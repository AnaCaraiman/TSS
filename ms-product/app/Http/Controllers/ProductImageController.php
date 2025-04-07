<?php

namespace App\Http\Controllers;

use App\Services\ProductImageService;
use App\Transformers\ProductImageTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function __construct(protected ProductImageService $productImageService) {}

    public function createImage(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $productImage = ProductImageTransformer::transform($data);
            $this->productImageService->createImage($productImage);

            return response()->json([
                'message' => 'Image added successfully'
            ], 201);

        }
        catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function getImagesByProductId(Request $request): JsonResponse
    {
        try {
            $id = $request->route('id');
            $images = $this->productImageService->getImagesByProductId($id);
            return response()->json([
                'message' => 'Images retrieved successfully',
                'images' => $images
            ], 201);
        }
        catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function deleteImage(Request $request): JsonResponse
    {
        try {
            $id = $request->route('id');
            $this->productImageService->deleteImage($id);

            return response()->json([
                'message' => 'Image deleted successfully'
            ], 201);

        }
        catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

}
