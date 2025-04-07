<?php

namespace App\Transformers;

use App\Validators\CartItemValidator;
use Exception;
use Illuminate\Support\Facades\Validator;

class CartItemTransformer
{

    /**
     * @throws Exception
     */
    public static function transform(array $data): array {
        $validator = Validator::make($data, CartItemValidator::rules, CartItemValidator::messages);

        if($validator->fails()) {
            throw new Exception(implode(' ', $validator->errors()->all()));
        }

        return [
            'user_id' => $data['user_id'],
            'product_id' => $data['product_id'],
            'operation' => $data['operation'] ?? null
        ];
    }

}
