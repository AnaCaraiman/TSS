<?php

namespace App\Validators;

class CartValidator
{
    public const rules = [
        'user_id' => 'required|integer'
    ];

    public const messages = [
        'user_id.required' => 'user_id is required',
        'user_id.integer' => 'user_id must be an integer'
    ];

}
