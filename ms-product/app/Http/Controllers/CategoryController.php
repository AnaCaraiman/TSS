<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\ProductService;
use App\Transformers\CategoryTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService,
    protected ProductService $productService) {}


    public function createCategory(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $category = CategoryTransformer::transform($data);
            $this->categoryService->createCategory($category);

            return response()->json([
                    'message' => 'Category created successfully'
                ], 201);
        }
        catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function getCategories(): JsonResponse
    {
        return response()->json([
            'message' => 'Categories retrieved successfully',
            'categories' => $this->categoryService->getAllCategories(),
            'brands' => $this->productService->getBrands(),
        ],201);
    }

    public function getCategory(Request $request): JsonResponse
    {
        try {
            $id = $request->route('id');
            $category = $this->categoryService->getCategoryById($id);

            return response()->json([
                'message' => 'Category with id ' . $id . ' retrieved successfully',
                'category' => $category
            ], 201);
        }
        catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function deleteCategory(Request $request): JsonResponse
    {
        try {
            $id = $request->route('id');
            $this->categoryService->deleteCategory($id);

            return response()->json([
                'message' => 'Category deleted successfully'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

}
