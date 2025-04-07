<?php

namespace App\Repositories;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductImageRepository
{

    /**
     * @throws Exception
     */
    public function createProductImage(array $data): bool
    {
        $existingPrimaryImage = DB::table('product_images')
            ->where('product_id', $data['product_id'] ?? null)
            ->where('is_primary', 1)
            ->first();

        if ($data['is_primary'] === true && $existingPrimaryImage) {
            throw new Exception('The product already has a primary image.');
        }
        return DB::table('product_images')->insert([
            'product_id' => $data['product_id'] ?? null,
            'image_url' => $data['image_url'] ?? '',
            'is_primary' => $data['is_primary'] ?? '',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function getImagesByProductId(int $productId): array
    {
        return DB::table('product_images')
            ->where('product_id', $productId)
            ->get()->toArray();
    }

    public function removeImageById(int $imageId): bool
    {
        return DB::table('product_images')
                ->where('id', $imageId)
                ->delete() > 0;
    }



}
