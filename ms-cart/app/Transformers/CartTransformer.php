<?php

namespace App\Transformers;

use App\Validators\CartValidator;
use Exception;
use Illuminate\Support\Facades\Validator;

class CartTransformer
{
    /**
     * @throws Exception
     */
    public static function transform(array $data): array {
        $validator = Validator::make($data, CartValidator::rules,CartValidator::messages);

        if($validator->fails()) {
            throw new Exception(implode(' ', $validator->errors()->all()));
        }

        return [
            'user_id' => $data['user_id']
        ];
    }
}
