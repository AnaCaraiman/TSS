<?php

namespace App\Repositories;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryRepository
{
    public function createCategory(array $data): bool
    {
        return DB::table('categories')->insert([
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function getAllCategories(): array
    {
        return DB::table('categories')
            ->get()
            ->toArray();
    }

    public function getCategoryById($id): ?object
    {
        return DB::table('categories')
            ->where('id', $id)
            ->first();
    }


    public function removeCategory($id): bool
    {
        return DB::table('categories')->where('id', $id)->delete() > 0;
    }

}
