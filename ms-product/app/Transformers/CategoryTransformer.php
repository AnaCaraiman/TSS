<?php

namespace App\Transformers;

use App\Validators\CategoryValidator;
use Exception;
use Illuminate\Support\Facades\Validator;

class CategoryTransformer
{
    /**
     * @throws Exception
     */
    public static function transform(array $data): array {
        $validator = Validator::make($data, CategoryValidator::rules,CategoryValidator::messages);

        if($validator->fails()){
            throw new Exception(implode(' ', $validator->errors()->all()));
        }

        return [
            'name' => $data['name'],
            'description' => $data['description']
        ];
    }

}
