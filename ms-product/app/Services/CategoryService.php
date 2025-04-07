<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Exception;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    public function __construct(public CategoryRepository $categoryRepository) {}

    public function getAllCategories(): array
    {
        $cached = Cache::get('categories');
        if ($cached) {
            return $cached;
        }
        $categories = $this->categoryRepository->getAllCategories();
        Cache::put('categories', $categories);
        return $categories;
    }

    /**
     * @throws Exception
     */
    public function getCategoryById(int $id): object
    {
        $category = $this->categoryRepository->getCategoryById($id);
        if($category === null) {
            throw new Exception('Category not found');
        }
        return $category;

    }

    /**
     * @throws Exception
     */
    public function createCategory(array $data): void
    {
        if(!$this->categoryRepository->createCategory($data)){
            throw new Exception('Category not created.');
        }

        Cache::forget('categories');
        Cache::put('categories',$this->categoryRepository->getAllCategories());
    }

    /**
     * @throws Exception
     */
    public function deleteCategory(int $id): void
    {
        if(!$this->categoryRepository->removeCategory($id)){
            throw new Exception('Category with id ' . $id . ' not deleted');
        }
        Cache::forget('categories');
        Cache::put('categories',$this->categoryRepository->getAllCategories());

    }
}
