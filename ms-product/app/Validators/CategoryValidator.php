<?php

namespace App\Validators;

class CategoryValidator
{
    public const rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    public const messages = [
        'name.required' => 'Name field is required.',
        'name.string' => 'Name must be a valid string.',
        'name.max' => 'Name may not exceed 255 characters.',

        'description.string' => 'Description must be a valid string.',
        'description.max' => 'Description may not exceed 1000 characters.',
    ];

}
