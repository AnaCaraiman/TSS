<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductAttributeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected int $productId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productId = DB::table('products')->insertGetId([
            'name' => 'Test Product',
            'brand' => 'TestBrand',
            'price' => 99.99,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function it_creates_a_product_attribute_successfully()
    {
        $payload = [
            'product_id' => $this->productId,
            'attribute_name' => 'Color',
            'attribute_value' => 'Red',
        ];

        $response = $this->postJson('/api/ms-product/attribute', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Attribute created successfully']);

        $this->assertDatabaseHas('product_attributes', [
            'product_id' => $this->productId,
            'attribute_name' => 'Color',
            'attribute_value' => 'Red',
        ]);
    }

    #[Test]
    public function it_fails_to_create_attribute_with_invalid_data()
    {
        $response = $this->postJson('/api/ms-product/attribute', [
            'product_id' => 'not-a-number',
            'attribute_name' => '',
            'attribute_value' => '',
        ]);

        $response->assertStatus(400);
        $this->assertStringContainsString('Product ID must be an integer.', $response->json('message'));
        $this->assertStringContainsString('Attribute name is required.', $response->json('message'));
        $this->assertStringContainsString('Attribute value is required.', $response->json('message'));
    }

    #[Test]
    public function it_gets_all_attributes_for_a_product()
    {
        DB::table('product_attributes')->insert([
            'product_id' => $this->productId,
            'attribute_name' => 'Size',
            'attribute_value' => 'Large',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson("/api/ms-product/attribute/{$this->productId}");

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Attributes retrieved successfully']);
        $this->assertCount(1, $response->json('attributes'));
    }

    #[Test]
    public function it_deletes_an_attribute_successfully()
    {
        $attributeId = DB::table('product_attributes')->insertGetId([
            'product_id' => $this->productId,
            'attribute_name' => 'Material',
            'attribute_value' => 'Cotton',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->deleteJson("/api/ms-product/attribute?attribute_id={$attributeId}");

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'message' => "Attribute with id {$attributeId} deleted successfully"
        ]);

        $this->assertDatabaseMissing('product_attributes', ['id' => $attributeId]);
    }

    #[Test]
    public function it_fails_to_delete_attribute_without_id()
    {
        $response = $this->deleteJson('/api/ms-product/attribute');

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'error' => 'Product and attribute id is required',
        ]);
    }

    #[Test]
    public function it_fails_to_delete_nonexistent_attribute()
    {
        $response = $this->deleteJson('/api/ms-product/attribute?attribute_id=99999');

        $response->assertStatus(400);
        $this->assertStringContainsString('not removed', $response->json('message'));
    }
}
