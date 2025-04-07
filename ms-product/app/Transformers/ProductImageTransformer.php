<?php

namespace App\Transformers;

use App\Validators\ProductImageValidator;
use Exception;
use Illuminate\Support\Facades\Validator;

class ProductImageTransformer
{
    public static function transform(array $data):array {
        $validator = Validator::make($data,ProductImageValidator::rules,ProductImageValidator::messages);

        if($validator->fails()){
            throw new Exception(implode(' ', $validator->errors()->all()));
        }

        return [
            'product_id' => (int) $data['product_id'],
            'image_url' => $data['image_url'],
            'is_primary' => (bool) $data['is_primary'],
        ];
    }

}
