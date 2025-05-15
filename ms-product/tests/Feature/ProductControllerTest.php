<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected int $categoryId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryId = DB::table('categories')->insertGetId([
            'name' => 'Electronics',
            'description' => 'Gadgets and tech',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function it_creates_a_product_successfully()
    {
        $payload = [
            'name' => 'iPhone',
            'brand' => 'Apple',
            'description' => 'Smartphone',
            'price' => 999.99,
            'stock_quantity' => 50,
            'category_id' => $this->categoryId,
        ];

        $response = $this->postJson('/api/ms-product', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Product created successfully']);
        $this->assertDatabaseHas('products', [
            'name' => 'iPhone',
            'brand' => 'Apple',
        ]);
    }

    #[Test]
    public function it_fails_to_create_product_with_invalid_data()
    {
        $payload = [
            'name' => '',
            'brand' => '',
            'description' => '',
            'price' => -5,
            'stock_quantity' => -2,
            'category_id' => 999,
        ];

        $response = $this->postJson('/api/ms-product', $payload);

        $response->assertStatus(400);
        $this->assertStringContainsString('Product name is required.', $response->json('message'));
        $this->assertStringContainsString('Brand name is required.', $response->json('message'));
        $this->assertStringContainsString('Product description is required.', $response->json('message'));
        $this->assertStringContainsString('Product price must be at least 0.01.', $response->json('message'));
        $this->assertStringContainsString('Stock quantity must be at least 0.', $response->json('message'));
        $this->assertStringContainsString('The selected category id is invalid.', $response->json('message'));
    }

    #[Test]
    public function it_returns_all_products()
    {
        DB::table('products')->insert([
            'name' => 'Monitor',
            'brand' => 'LG',
            'description' => 'Display screen',
            'price' => 199.99,
            'stock_quantity' => 30,
            'category_id' => $this->categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/ms-product');

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Products retrieved successfully']);
        $this->assertCount(1, $response->json('products'));
    }

    #[Test]
    public function it_returns_a_product_by_id()
    {
        $id = DB::table('products')->insertGetId([
            'name' => 'Keyboard',
            'brand' => 'Logitech',
            'description' => 'Mechanical keyboard',
            'price' => 89.99,
            'stock_quantity' => 15,
            'category_id' => $this->categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson("/api/ms-product/$id");

        $response->assertStatus(201);
        $this->assertEquals('Keyboard', $response->json('product.name'));
    }

    #[Test]
    public function it_returns_400_if_product_id_not_found()
    {
        $response = $this->getJson('/api/ms-product/99999');

        $response->assertStatus(400);
        $this->assertStringContainsString('Product not found', $response->json('message'));
    }

    #[Test]
    public function it_deletes_a_product_successfully()
    {
        $id = DB::table('products')->insertGetId([
            'name' => 'Speaker',
            'brand' => 'Sony',
            'description' => 'Bluetooth speaker',
            'price' => 59.99,
            'stock_quantity' => 20,
            'category_id' => $this->categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->deleteJson("/api/ms-product/$id");

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Product deleted successfully']);
        $this->assertDatabaseMissing('products', ['id' => $id]);
    }

    #[Test]
    public function it_fails_to_delete_product_if_not_found()
    {
        $response = $this->deleteJson('/api/ms-product/99999');

        $response->assertStatus(400);
        $this->assertStringContainsString('Product with id 99999 not found.', $response->json('message'));
    }

    #[Test]
    public function it_fetches_products_by_ids()
    {
        $id1 = DB::table('products')->insertGetId([
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'description' => 'Wireless mouse',
            'price' => 29.99,
            'stock_quantity' => 40,
            'category_id' => $this->categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $id2 = DB::table('products')->insertGetId([
            'name' => 'Charger',
            'brand' => 'Anker',
            'description' => 'Fast USB-C charger',
            'price' => 39.99,
            'stock_quantity' => 25,
            'category_id' => $this->categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/ms-product/productsbyids?ids[]=' . $id1 . '&ids[]=' . $id2);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Products retrieved successfully']);
        $this->assertCount(2, $response->json('products'));
    }
}
