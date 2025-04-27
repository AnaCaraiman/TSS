<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Services\CategoryService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $categoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryService = app(CategoryService::class);
    }

    /** @test */
    public function it_gets_all_categories_successfully()
    {
        Category::factory()->create(['name' => 'Category 1']);
        Category::factory()->create(['name' => 'Category 2']);

        Cache::forget('categories');

        $categories = $this->categoryService->getAllCategories();

        $this->assertCount(2, $categories);
    }

    /** @test */
    public function it_creates_category_successfully()
    {
        Cache::forget('categories');

        $this->categoryService->createCategory([
            'name' => 'New Category',
            'description' => 'Test description',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'description' => 'Test description',
        ]);
    }

    /** @test */
    public function it_throws_exception_when_getting_nonexistent_category()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Category not found');

        $this->categoryService->getCategoryById(9999);
    }

    /** @test */
    public function it_deletes_category_successfully()
    {
        $category = Category::factory()->create();

        $this->categoryService->deleteCategory($category->id);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}
