<?php

namespace App\Validators;

class CartItemValidator
{
    public const rules = [
        'user_id' => 'required|integer|exists:carts,user_id',
        'product_id' => 'required|integer',
        'operation' => 'in:-,+',
    ];

    public const messages = [
        'user_id.required' => 'User ID field is required.',
        'user_id.integer' => 'User ID must be an integer.',
        'product_id.required' => 'Product ID field is required.',
        'product_id.integer' => 'Product ID must be an integer.',
        'operation.required' => 'Operation field is required.',
        'operation.in' => 'Operation must be either "+" (add) or "-" (remove).',
    ];
}

