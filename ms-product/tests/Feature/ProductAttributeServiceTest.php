<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\ProductAttributeService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductAttributeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $productAttributeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productAttributeService = app(ProductAttributeService::class);
    }

    /** @test */
    public function it_creates_attribute_successfully()
    {
        $product = Product::factory()->create();

        $this->productAttributeService->createAttribute($product->id, [
            'attribute_name' => 'Size',
            'attribute_value' => 'Large',
        ]);

        $this->assertDatabaseHas('product_attributes', [
            'product_id' => $product->id,
            'attribute_name' => 'Size',
            'attribute_value' => 'Large',
        ]);
    }

    /** @test */
    public function it_throws_exception_when_creating_attribute_with_invalid_data()
    {
        $product = Product::factory()->create();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Attribute not created for product with id ' . $product->id);

        $this->productAttributeService->createAttribute($product->id, [
            // No 'attribute_name' or 'attribute_value'
        ]);
    }

    /** @test */
    public function it_gets_product_attributes_successfully()
    {
        $product = Product::factory()->create();

        DB::table('product_attributes')->insert([
            'product_id' => $product->id,
            'attribute_name' => 'Material',
            'attribute_value' => 'Cotton',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $attributes = $this->productAttributeService->getProductAttributes($product->id);

        $this->assertCount(1, $attributes);
        $this->assertEquals('Material', $attributes[0]->attribute_name);
        $this->assertEquals('Cotton', $attributes[0]->attribute_value);
    }

    /** @test */
    public function it_removes_attribute_successfully()
    {
        $product = Product::factory()->create();

        $attributeId = DB::table('product_attributes')->insertGetId([
            'product_id' => $product->id,
            'attribute_name' => 'Weight',
            'attribute_value' => '500g',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->productAttributeService->removeAttribute($attributeId);

        $this->assertDatabaseMissing('product_attributes', [
            'id' => $attributeId,
        ]);
    }

    /** @test */
    public function it_throws_exception_when_removing_nonexistent_attribute()
    {
        $nonexistentId = 9999;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Attribute with id ' . $nonexistentId . ' not removed');

        $this->productAttributeService->removeAttribute($nonexistentId);
    }
}
