<?php

namespace App\Validators;

class PaymentValidator
{
    public const rules = [
        'user_id' => 'required|integer',
        'stripe_token' => 'required|string',
        'price' => 'required|numeric|min:1',
    ];

    public const messages = [
        'user_id.required' => 'user_id is required',
        'user_id.integer' => 'user_id must be an integer',
        'stripe_token.required' => 'stripe_token is required',
        'stripe_token.string' => 'stripe_token must be a string',
        'price.required' => 'amount is required',
        'price.min' => 'amount must be at least 1',
    ];
}
