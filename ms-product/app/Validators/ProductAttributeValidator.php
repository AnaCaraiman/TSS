<?php

namespace App\Validators;

class ProductAttributeValidator
{

    public const rules = [

        'product_id' => 'required|integer|exists:products,id',
        'attribute_name' => 'required|string|max:50',
        'attribute_value' => 'required|string|max:100'
    ];

    public const messages = [
        'product_id.required' => 'Product ID is required.',
        'product_id.integer' => 'Product ID must be an integer.',
        'product_id.exists' => 'Specified product ID does not exist.',

        'attribute_name.required' => 'Attribute name is required.',
        'attribute_name.string' => 'Attribute name must be a string.',
        'attribute_name.max' => 'Attribute name must not exceed 50 characters.',

        'attribute_value.required' => 'Attribute value is required.',
        'attribute_value.string' => 'Attribute value must be a string.',
        'attribute_value.max' => 'Attribute value must not exceed 100 characters.',
    ];
}
