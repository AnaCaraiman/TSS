<?php

namespace App\Validators;

class FavoriteItemValidator
{
    public const rules = [
        'favorite_id' => 'required|integer|exists:favorites,id',
        'product_id'  => 'required|integer',
        'name'        => 'required|string|max:255',
        'image_url'   => 'nullable|url|max:2048',
        'price'       => 'required|numeric|min:0',
    ];

    public const messages = [
        'favorite_id.required' => 'Favorite ID is required.',
        'favorite_id.integer' => 'Favorite ID must be an integer.',
        'favorite_id.exists' => 'The specified favorite ID does not exist.',

        'product_id.required' => 'Product ID is required.',
        'product_id.integer' => 'Product ID must be an integer.',

        'name.required' => 'Product name is required.',
        'name.string' => 'Product name must be a string.',
        'name.max' => 'Product name must not exceed 255 characters.',

        'image_url.url' => 'Image URL must be a valid URL.',
        'image_url.max' => 'Image URL must not exceed 2048 characters.',

        'price.required' => 'Price is required.',
        'price.numeric' => 'Price must be a number.',
        'price.min' => 'Price must be at least 0.',
    ];
}
