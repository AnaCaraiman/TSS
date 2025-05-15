<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_category_successfully()
    {
        $payload = [
            'name' => 'Electronics',
            'description' => 'Gadgets and devices',
        ];

        $response = $this->postJson('/api/ms-product/category', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Category created successfully']);

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
            'description' => 'Gadgets and devices',
        ]);
    }

    #[Test]
    public function it_fails_to_create_category_with_invalid_data()
    {
        $response = $this->postJson('/api/ms-product/category', [
            'name' => '', // required
        ]);

        $response->assertStatus(400);
        $this->assertStringContainsString('Name field is required.', $response->json('message'));
    }

    #[Test]
    public function it_gets_all_categories_and_brands()
    {
        DB::table('categories')->insert([
            ['name' => 'Tech', 'description' => 'Tech stuff', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Assuming brands come from products; simulate product data
        DB::table('products')->insert([
            'name' => 'Laptop',
            'brand' => 'Dell',
            'price' => 1200,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Cache::forget('categories'); // clear to test fresh pull

        $response = $this->getJson('/api/ms-product/category');

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Categories retrieved successfully']);
        $this->assertArrayHasKey('categories', $response->json());
        $this->assertArrayHasKey('brands', $response->json());
    }

    #[Test]
    public function it_retrieves_single_category()
    {
        $id = DB::table('categories')->insertGetId([
            'name' => 'Books',
            'description' => 'Books category',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->getJson("/api/ms-product/category/$id");

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'message' => "Category with id $id retrieved successfully",
        ]);
        $this->assertEquals('Books', $response->json('category.name'));
    }

    #[Test]
    public function it_fails_to_retrieve_invalid_category()
    {
        $response = $this->getJson('/api/ms-product/category/999');

        $response->assertStatus(400);
        $this->assertStringContainsString('Category not found', $response->json('message'));
    }

    #[Test]
    public function it_deletes_a_category_successfully()
    {
        $id = DB::table('categories')->insertGetId([
            'name' => 'DeleteMe',
            'description' => 'To be deleted',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->deleteJson("/api/ms-product/category/$id");

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Category deleted successfully']);
        $this->assertDatabaseMissing('categories', ['id' => $id]);
    }

    #[Test]
    public function it_fails_to_delete_nonexistent_category()
    {
        $response = $this->deleteJson('/api/ms-product/category/999');

        $response->assertStatus(400);
        $this->assertStringContainsString('not deleted', $response->json('message'));
    }
}
