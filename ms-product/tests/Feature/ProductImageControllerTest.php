<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductImageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected int $productId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productId = DB::table('products')->insertGetId([
            'name' => 'Camera',
            'brand' => 'Canon',
            'description' => 'DSLR camera',
            'price' => 499.99,
            'stock_quantity' => 10,
            'category_id' => DB::table('categories')->insertGetId([
                'name' => 'Photography',
                'description' => 'Cameras and gear',
                'created_at' => now(),
                'updated_at' => now(),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function it_creates_a_product_image_successfully()
    {
        $payload = [
            'product_id' => $this->productId,
            'image_url' => 'https://example.com/img.jpg',
            'is_primary' => true,
        ];

        $response = $this->postJson('/api/ms-product/image', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Image added successfully']);
        $this->assertDatabaseHas('product_images', [
            'product_id' => $this->productId,
            'image_url' => 'https://example.com/img.jpg',
            'is_primary' => 1,
        ]);
    }

    #[Test]
    public function it_fails_to_create_image_with_invalid_data()
    {
        $response = $this->postJson('/api/ms-product/image', [
            'product_id' => 'invalid',
            'image_url' => 123,
            'is_primary' => 'yes',
        ]);

        $response->assertStatus(400);
        $this->assertStringContainsString('Product ID must be an integer.', $response->json('message'));
        $this->assertStringContainsString('Image URL must be a valid string.', $response->json('message'));
        $this->assertStringContainsString('Primary flag must be true or false.', $response->json('message'));
    }

    #[Test]
    public function it_fails_if_duplicate_primary_image_is_added()
    {
        DB::table('product_images')->insert([
            'product_id' => $this->productId,
            'image_url' => 'https://example.com/first.jpg',
            'is_primary' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson('/api/ms-product/image', [
            'product_id' => $this->productId,
            'image_url' => 'https://example.com/second.jpg',
            'is_primary' => true,
        ]);

        $response->assertStatus(400);
        $this->assertStringContainsString('already has a primary image', $response->json('message'));
    }

    #[Test]
    public function it_gets_images_by_product_id()
    {
        DB::table('product_images')->insert([
            'product_id' => $this->productId,
            'image_url' => 'https://example.com/photo.jpg',
            'is_primary' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson("/api/ms-product/image/$this->productId");

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Images retrieved successfully']);
        $this->assertCount(1, $response->json('images'));
    }

    #[Test]
    public function it_deletes_an_image_successfully()
    {
        $imageId = DB::table('product_images')->insertGetId([
            'product_id' => $this->productId,
            'image_url' => 'https://example.com/delete.jpg',
            'is_primary' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->deleteJson("/api/ms-product/image/$imageId");

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Image deleted successfully']);
        $this->assertDatabaseMissing('product_images', ['id' => $imageId]);
    }

    #[Test]
    public function it_fails_to_delete_nonexistent_image()
    {
        $response = $this->deleteJson('/api/ms-product/image/99999');

        $response->assertStatus(400);
        $this->assertStringContainsString('No picture was deleted', $response->json('message'));
    }
}
