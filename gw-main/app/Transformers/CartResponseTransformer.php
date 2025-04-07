<?php

namespace App\Transformers;

use Illuminate\Http\Client\Response;

class CartResponseTransformer
{
    public static function transform(Response $response,string $key){
        $responseBody = json_decode($response->getBody()->getContents(), true);
        return $responseBody[$key] ?? $responseBody;
    }

}
