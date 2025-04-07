<?php

namespace App\Transformers;

use App\Validators\ProductAttributeValidator;
use Exception;
use Illuminate\Support\Facades\Validator;

class ProductAttributeTransformer
{
    public static function transform(array $data):array {
        $validator = Validator::make($data,ProductAttributeValidator::rules,ProductAttributeValidator::messages);

        if($validator->fails()){
            throw new Exception(implode(' ', $validator->errors()->all()));
        }

        return [
            'product_id' => (int) $data['product_id'],
            'attribute_name' => $data['attribute_name'],
            'attribute_value' => $data['attribute_value']
        ];
    }

}
