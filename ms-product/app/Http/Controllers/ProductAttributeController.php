<?php

namespace App\Http\Controllers;

use App\Services\ProductAttributeService;
use App\Transformers\ProductAttributeTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function __construct(protected ProductAttributeService $productAttributeService) {}

    public function createAttribute(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $productAttribute = ProductAttributeTransformer::transform($data);
            $this->productAttributeService->createAttribute($productAttribute['product_id'], array_slice($productAttribute, 1));

            return response()->json([
                'message' => 'Attribute created successfully'
            ], 201);
        }
        catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function getAttributes(Request $request): JsonResponse
    {
        $id = $request->route('id');

        $attributes = $this->productAttributeService->getProductAttributes($id);

        return response()->json([
            'message' => 'Attributes retrieved successfully',
            'attributes' => $attributes
        ],201);
    }

    public function deleteAttribute(Request $request): JsonResponse
    {
        try{
        $attributeId = $request->query('attribute_id');
        if($attributeId === null) {
            return response()->json(
                ['error' => 'Product and attribute id is required']
            ,400);

        }

        $this->productAttributeService->removeAttribute($attributeId);

        return response()->json([
                'message' => 'Attribute with id ' . $attributeId . ' deleted successfully'
            ],201);
        }
        catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

}
