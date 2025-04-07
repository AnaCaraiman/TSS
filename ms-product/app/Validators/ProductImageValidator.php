<?php

namespace App\Validators;

class ProductImageValidator
{
    public const rules = [
        'product_id' => 'required|integer|exists:products,id',
        'image_url' => 'required|string|max:255',
        'is_primary' => 'required|boolean'
    ];

    public const messages = [

        'product_id.required' => 'Product ID is required.',
        'product_id.integer' => 'Product ID must be an integer.',
        'product_id.exists' => 'Specified product ID does not exist.',

        'image_url.required' => 'Image URL is required.',
        'image_url.string' => 'Image URL must be a valid string.',
        'image_url.max' => 'Image URL must not exceed 255 characters.',

        'is_primary.required' => 'Primary flag is required.',
        'is_primary.boolean' => 'Primary flag must be true or false.',
    ];

}
