<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CatalogService
{
    public function __construct(){}


    public function readQueryParams(Request $request): array
    {
        return [
            'name' => $request->query('name'),
            'brand' => $request->query('brand'),
            'category_id' => $request->query('category_id'),
            'min_price' => is_numeric($request->query('min_price')) ? (float) $request->query('min_price') : null,
            'max_price' => is_numeric($request->query('max_price')) ? (float) $request->query('max_price') : null,
            'in_stock' => $request->has('in_stock') ? $request->boolean('in_stock') : null,
            'sort_by' => $this->validateSortParam($request->query('sort_by')),
            'page' => max(1, (int) $request->query('page', 1))
        ];
    }

    public function filterProducts(Collection $products, array $filters): Collection
    {
        if($filters['brand'] !== null) {
            $products = $this->applyBrandFilter($products,$filters);
        }
        if ($filters['min_price'] !== null || $filters['max_price'] !== null) {
            $products = $this->applyPriceFilter($products, $filters);
        }

        if ($filters['name'] !== null) {
            $products = $this->applyNameFilter($products, $filters);
        }

        if ($filters['in_stock']) {
            $products = $this->applyStockFilter($products);
        }

        if ($filters['category_id'] !== null) {
            $products = $this->applyCategoryFilter($products, $filters);
        }

        return $this->applySorting($products, $filters);
    }

    private function applyPriceFilter(Collection $products, array $filters): Collection
    {
        if ($filters['min_price'] !== null) {
            $products = $products->where('price', '>=', $filters['min_price']);
        }
        if ($filters['max_price'] !== null) {
            $products = $products->where('price', '<=', $filters['max_price']);
        }
        return $products;
    }

    private function applyNameFilter(Collection $products, array $filters): Collection
    {
        $searchTerm = strtolower($filters['name']);
        return $products->filter(function ($product) use ($searchTerm) {
            return str_contains(strtolower($product['name']), $searchTerm);
        });
    }

    private function applyStockFilter(Collection $products): Collection
    {
        return $products->where('stock_quantity', '>', 0);
    }

    private function applyCategoryFilter(Collection $products, array $filters): Collection
    {
        return $products->where('category_id', $filters['category_id']);
    }

    private function applySorting(Collection $products, array $filters): Collection
    {
        [$sortField, $sortDirection] = $filters['sort_by'];
        return $products->sortBy($sortField, SORT_REGULAR, $sortDirection === 'desc');
    }

    private function validateSortParam(?string $sortBy): array
    {
        $validSortOptions = [
            ['price', 'asc'],
            ['price', 'desc'],
            ['name', 'asc'],
            ['name', 'desc'],
            ['created_at', 'desc']
        ];

        foreach ($validSortOptions as $option) {
            if ($sortBy === $option[0] . '_' . $option[1]) {
                return $option;
            }
        }

        return ['id', 'asc'];
    }

    private function applyBrandFilter(Collection $products, array $filters): Collection
    {
        return $products->where('brand', $filters['brand']);
    }

}
