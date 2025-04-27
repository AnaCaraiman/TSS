<?php
namespace App\Http\Controllers;

use App\Services\CatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Exception;

class CatalogController extends Controller
{

    private string $productServiceUrl;
    public function __construct(public CatalogService $catalogService)
    {
        $this->productServiceUrl = config('services.ms_product.url');
    }

    public function getCatalog(Request $request): JsonResponse
    {
        try {
            $filters = $this->catalogService->readQueryParams($request);

            $response = Http::get($this->productServiceUrl . '/api/ms-product');

            $data = json_decode($response->getBody()->getContents(), true);
            $products = collect($data['products']);

            $filteredProducts = $this->catalogService->filterProducts($products, $filters);

            $availableBrands = $filteredProducts->pluck('brand')->unique()->values();
            $availableCategories = $filteredProducts->pluck('category')->unique('id')->map(function ($category) {
                return [
                    'id' => $category['id'],
                    'name' => $category['name'],
                ];
            })->values();

            $perPage = $request->query('per_page', 30);
            $page = $request->query('page', 1);
            $total = $filteredProducts->count();

            $paginatedProducts = new LengthAwarePaginator(
                $filteredProducts->forPage($page, $perPage),
                $total,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return response()->json([
                'message' => 'Products filtered successfully',
                'products' => $paginatedProducts->items(),
                'pagination' => [
                    'total' => $total,
                    'count' => count($paginatedProducts->items()),
                    'per_page' => $perPage,
                    'current_page' => $paginatedProducts->currentPage(),
                    'total_pages' => $paginatedProducts->lastPage(),
                ],
                'filters' => [
                    'brands' => $availableBrands,
                    'categories' => $availableCategories,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Service unavailable',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}

