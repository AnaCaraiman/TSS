<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ProductAttributeRepository
{
    public function getAllAtributesByProductId(int $id): array
    {
        return DB::table('product_attributes')
            ->where('product_id', $id)
            ->get()
            ->toArray();
    }

    public function createAttributeForProduct(int $productId, array $attributeData): bool
    {
        return DB::table('product_attributes')
            ->insert([
                'product_id' => $productId,
                'attribute_name' => $attributeData['attribute_name'],
                'attribute_value' => $attributeData['attribute_value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }


    public function removeAttributeFromProduct(int $attributeId): bool
    {
        return DB::table('product_attributes')
            ->where('id', $attributeId)
            ->delete() >0;
    }


}
