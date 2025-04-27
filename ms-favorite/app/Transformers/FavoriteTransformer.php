<?php

namespace App\Transformers;

use App\Models\Favorite;
use App\Validators\FavoriteValidator;
use Exception;
use Illuminate\Support\Facades\Validator;

class FavoriteTransformer
{
    /**
     * @throws Exception
     */
    public static function transform(array $data): array {
        $validator = Validator::make($data, FavoriteValidator::rules,FavoriteValidator::messages);

        if($validator->fails()){
            throw new Exception(implode(' ', $validator->errors()->all()));
        }

        return [
            'user_id' => $data['user_id'],
        ];
    }

}
