<?php

namespace App\Transformers;

use App\Validators\FavoriteItemValidator;
use Exception;
use Illuminate\Support\Facades\Validator;

class FavoriteItemTransformer
{
    /**
     * @throws Exception
     */
    public static function transform(array $data): array {
        $validator =Validator::make($data, FavoriteItemValidator::rules, FavoriteItemValidator::messages);

        if($validator->fails()) {
            throw new Exception(implode(' ', $validator->errors()->all()));
        }

        return [
            'favorite_id' => $data['favorite_id'],
            'product_id'  => $data['product_id'],
            'name'        => $data['name'],
            'image_url'   => $data['image_url'],
            'price'       => $data['price'],
        ];
    }
}
