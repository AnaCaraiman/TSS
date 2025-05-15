<?php

namespace Tests\Feature\Catalog;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_endpoint_returns_filtered_products()
    {
        Http::fake([
            '*' => Http::response([
                'products' => [
                    [
                        'id' => 1,
                        'name' => 'Test Phone',
                        'price' => 199.99,
                        'stock_quantity' => 5,
                        'category_id' => 1,
                        'brand' => 'BrandX',
                        'created_at' => now()->toISOString(),
                        'category' => ['id' => 1, 'name' => 'Electronics'],
                    ],
                    [
                        'id' => 2,
                        'name' => 'Test Laptop',
                        'price' => 999.99,
                        'stock_quantity' => 0,
                        'category_id' => 1,
                        'brand' => 'BrandY',
                        'created_at' => now()->toISOString(),
                        'category' => ['id' => 1, 'name' => 'Electronics'],
                    ]
                ]
            ], 200)
        ]);

        $response = $this->getJson('/api/ms-catalog?min_price=100&in_stock=true');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'products',
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages',
                ],
                'filters' => [
                    'brands',
                    'categories',
                ]
            ])
            ->assertJsonFragment(['message' => 'Products filtered successfully']);
    }
}