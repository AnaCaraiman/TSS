<?php

namespace App\Transformers;

use App\Enums\OrderStatus;

use App\Validators\OrderValidator;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;


class OrderTransformer
{
    public static function transform(array $data): array
    {
        $validator = Validator::make($data, OrderValidator::rules(),OrderValidator::messages());

        if($validator->fails()) {
            throw new Exception(implode(' ', $validator->errors()->all()));
        }

        return [
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'street' => $data['street'],
            'number' => $data['number'],
            'additional_info' => $data['additional_info'] ?? null,
            'city' => $data['city'],
            'county' => $data['county'],
            'postcode' => $data['postcode'],
            'status' => OrderStatus::PENDING,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'payment_type' => $data['payment_type'],
            'price' => $data['price'],
            'products' => collect($data['products'])->map(function ($product) {
                return [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $product['quantity'],
                    'image_url' => $product['image_url'] ?? null,
                ];
            })->toArray(),
        ];

    }

}
