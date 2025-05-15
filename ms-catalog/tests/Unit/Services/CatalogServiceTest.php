<?php

namespace Tests\Unit\Services;

use App\Services\CatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Tests\TestCase as LaravelTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\AssertableJson;

class CatalogServiceTest extends LaravelTestCase
{
    use WithFaker;
    use RefreshDatabase;

    private CatalogService $catalogService;
    private Collection $sampleProducts;

    protected function setUp(): void
    {
        parent::setUp();
        $this->catalogService = new CatalogService();

        // Setup sample products for testing
        $this->sampleProducts = collect([
            [
                'id' => 1,
                'name' => 'Cheap Phone',
                'price' => 100,
                'stock_quantity' => 5,
                'category_id' => 1,
                'created_at' => '2025-01-20',
                'brand' => 'Apple'
            ],
            [
                'id' => 2,
                'name' => 'Expensive Phone',
                'price' => 1000,
                'stock_quantity' => 0,
                'category_id' => 1,
                'created_at' => '2025-01-21',
                'brand' => 'Apple'
            ],
            [
                'id' => 3,
                'name' => 'Kitchen Mixer',
                'price' => 200,
                'stock_quantity' => 10,
                'category_id' => 2,
                'created_at' => '2025-01-22',
                'brand' => 'KitchenAid'
            ]
        ]);

        Http::fake([
            '*/api/ms-product*' => Http::response($this->sampleProductResponse, 200)
        ]);
    }

    #[Test]
    public function it_filters_by_min_price()
    {
        $filters = $this->createFilters(['min_price' => 150]);
        $filtered = $this->catalogService->filterProducts($this->sampleProducts, $filters);

        $this->assertCount(2, $filtered);
        $this->assertTrue($filtered->every(fn($product) => $product['price'] >= 150));
    }

    #[Test]
    public function it_filters_by_max_price()
    {
        $filters = $this->createFilters(['max_price' => 500]);
        $filtered = $this->catalogService->filterProducts($this->sampleProducts, $filters);

        $this->assertCount(2, $filtered);
        $this->assertTrue($filtered->every(fn($product) => $product['price'] <= 500));
    }

    #[Test]
    public function it_filters_by_price_range()
    {
        $filters = $this->createFilters([
            'min_price' => 150,
            'max_price' => 500
        ]);
        $filtered = $this->catalogService->filterProducts($this->sampleProducts, $filters);

        $this->assertCount(1, $filtered);
        $this->assertEquals(200, $filtered->first()['price']);
    }

    #[Test]
    public function it_filters_by_name()
    {
        $filters = $this->createFilters(['name' => 'phone']);
        $filtered = $this->catalogService->filterProducts($this->sampleProducts, $filters);

        $this->assertCount(2, $filtered);
        $this->assertTrue($filtered->every(
            fn($product) =>
            str_contains(strtolower($product['name']), 'phone')
        ));
    }

    #[Test]
    public function it_filters_by_stock()
    {
        $filters = $this->createFilters(['in_stock' => true]);
        $filtered = $this->catalogService->filterProducts($this->sampleProducts, $filters);

        $this->assertCount(2, $filtered);
        $this->assertTrue($filtered->every(fn($product) => $product['stock_quantity'] > 0));
    }

    #[Test]
    public function it_filters_by_category()
    {
        $filters = $this->createFilters(['category_id' => 1]);
        $filtered = $this->catalogService->filterProducts($this->sampleProducts, $filters);

        $this->assertCount(2, $filtered);
        $this->assertTrue($filtered->every(fn($product) => $product['category_id'] === 1));
    }

    #[Test]
    public function it_filters_by_brand()
    {
        $filters = $this->createFilters(['brand' => 'Apple']);
        $filtered = $this->catalogService->filterProducts($this->sampleProducts, $filters);

        $this->assertCount(2, $filtered);
        $this->assertTrue($filtered->every(fn($product) => $product['brand'] === 'Apple'));
    }

    #[Test]
    public function it_sorts_by_price_asc()
    {
        $filters = $this->createFilters(['sort_by' => ['price', 'asc']]);
        $filtered = $this->catalogService->filterProducts($this->sampleProducts, $filters);

        $prices = $filtered->pluck('price')->all();
        $this->assertEquals([100, 200, 1000], $prices);
    }

    #[Test]
    public function it_sorts_by_price_desc()
    {
        $filters = $this->createFilters(['sort_by' => ['price', 'desc']]);
        $filtered = $this->catalogService->filterProducts($this->sampleProducts, $filters);

        $prices = $filtered->pluck('price')->all();
        $this->assertEquals([1000, 200, 100], $prices);
    }

    #[Test]
    public function it_sorts_by_name_asc()
    {
        $filters = $this->createFilters(['sort_by' => ['name', 'asc']]);
        $filtered = $this->catalogService->filterProducts($this->sampleProducts, $filters);

        $this->assertEquals('Cheap Phone', $filtered->first()['name']);
        $this->assertEquals('Kitchen Mixer', $filtered->last()['name']);
    }

    #[Test]
    public function it_combines_multiple_filters()
    {
        $filters = $this->createFilters([
            'min_price' => 50,
            'max_price' => 500,
            'in_stock' => true,
            'category_id' => 1,
            'sort_by' => ['price', 'desc']
        ]);

        $filtered = $this->catalogService->filterProducts($this->sampleProducts, $filters);

        $this->assertCount(1, $filtered);
        $this->assertEquals(100, $filtered->first()['price']);
        $this->assertEquals(1, $filtered->first()['category_id']);
        $this->assertTrue($filtered->first()['stock_quantity'] > 0);
    }

    #[Test]
    public function it_validates_query_params()
    {
        $request = Request::create('/', 'GET', [
            'min_price' => '100',
            'max_price' => 'invalid',
            'in_stock' => '1',
            'sort_by' => 'price_desc',
            'page' => '2'
        ]);

        $params = $this->catalogService->readQueryParams($request);

        $this->assertEquals(100.0, $params['min_price']);
        $this->assertNull($params['max_price']);
        $this->assertTrue($params['in_stock']);
        $this->assertEquals(['price', 'desc'], $params['sort_by']);
        $this->assertEquals(2, $params['page']);
    }

    private function createFilters(array $overrides = []): array
    {
        return array_merge([
            'name' => null,
            'brand' => null,
            'category_id' => null,
            'min_price' => null,
            'max_price' => null,
            'in_stock' => null,
            'sort_by' => ['id', 'asc'],
            'page' => 1
        ], $overrides);
    }

    private array $sampleProductResponse = [
        'message' => 'Products retrieved successfully for page 1',
        'products' => [
            [
                'id' => 1,
                'name' => 'Cheap Phone',
                'price' => 100,
                'stock_quantity' => 5,
                'category_id' => 1,
                'created_at' => '2025-01-20',
                'category' => [
                    'id' => 1,
                    'name' => 'Electronics'
                ],
                'images' => [
                    [
                        'id' => 1,
                        'product_id' => 1,
                        'image_url' => 'cheap-phone.jpg',
                        'is_primary' => 1
                    ]
                ]
            ],
            [
                'id' => 2,
                'name' => 'Expensive Phone',
                'price' => 1000,
                'stock_quantity' => 0,
                'category_id' => 1,
                'created_at' => '2025-01-21',
                'category' => [
                    'id' => 1,
                    'name' => 'Electronics'
                ],
                'images' => []
            ]
        ]
    ];
}
