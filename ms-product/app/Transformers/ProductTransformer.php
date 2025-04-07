<?php

namespace App\Transformers;

use App\Validators\ProductValidator;
use Exception;
use Illuminate\Support\Facades\Validator;

class ProductTransformer
{
    /**
     * @throws Exception
     */
    public static function transform(array $data): array
    {
        $validator = Validator::make($data, ProductValidator::rules,ProductValidator::messages);

        if($validator->fails()) {
            throw new Exception(implode(' ', $validator->errors()->all()));
        }

        return [
          'name' => $data['name'],
          'brand' => $data['brand'],
          'description' => $data['description'],
          'price' => (float) $data['price'],
          'stock_quantity' => (int) $data['stock_quantity'],
          'category_id' => (int) $data['category_id'],
        ];
    }


}
