<?php

namespace Tests\Unit;

use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    protected $categoryRepository;
    protected CategoryService $categoryService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = Mockery::mock(CategoryRepository::class);
        $this->categoryService = new CategoryService($this->categoryRepository);
    }

    /** @test */
    public function it_returns_all_categories_from_repository()
    {
        Cache::shouldReceive('get')->with('categories')->andReturn(null);
        Cache::shouldReceive('put')->once();

        $this->categoryRepository
            ->shouldReceive('getAllCategories')
            ->once()
            ->andReturn([['id' => 1, 'name' => 'Test']]);

        $categories = $this->categoryService->getAllCategories();

        $this->assertIsArray($categories);
        $this->assertEquals('Test', $categories[0]['name']);
    }

    /** @test */
    public function it_throws_exception_if_category_not_found()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Category not found');

        $this->categoryRepository
            ->shouldReceive('getCategoryById')
            ->with(999)
            ->andReturn(null);

        $this->categoryService->getCategoryById(999);
    }

    /** @test
     * @throws Exception
     */
    public function it_creates_a_category_successfully()
    {
        Cache::shouldReceive('forget')->once();
        Cache::shouldReceive('put')->once();

        $this->categoryRepository
            ->shouldReceive('createCategory')
            ->once()
            ->andReturn(true);

        $this->categoryRepository
            ->shouldReceive('getAllCategories')
            ->once()
            ->andReturn([]);

        $this->categoryService->createCategory(['name' => 'Test']);
        $this->assertTrue(true);
    }

    /** @test */
    public function it_throws_exception_when_create_category_fails()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Category not created.');

        $this->categoryRepository
            ->shouldReceive('createCategory')
            ->once()
            ->andReturn(false);

        $this->categoryService->createCategory(['name' => 'Test']);
    }

    /** @test
     * @throws Exception
     */
    public function it_deletes_a_category_successfully()
    {
        Cache::shouldReceive('forget')->once();
        Cache::shouldReceive('put')->once();

        $this->categoryRepository
            ->shouldReceive('removeCategory')
            ->with(1)
            ->once()
            ->andReturn(true);

        $this->categoryRepository
            ->shouldReceive('getAllCategories')
            ->once()
            ->andReturn([]);

        $this->categoryService->deleteCategory(1);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_throws_exception_when_delete_category_fails()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Category with id 1 not deleted');

        $this->categoryRepository
            ->shouldReceive('removeCategory')
            ->with(1)
            ->once()
            ->andReturn(false);

        $this->categoryService->deleteCategory(1);
    }
}
