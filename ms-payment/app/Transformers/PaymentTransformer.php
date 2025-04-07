<?php

namespace App\Transformers;

use App\Validators\PaymentValidator;
use Exception;
use Illuminate\Support\Facades\Validator;

class PaymentTransformer
{
    /**
     * @throws Exception
     */
    public static function transform(array $data): array{
        $validator = Validator::make($data,PaymentValidator::rules,PaymentValidator::messages );

        if($validator->fails()){
            throw new Exception(implode(' ', $validator->errors()->all()));
        }

        return $data;

    }

}
