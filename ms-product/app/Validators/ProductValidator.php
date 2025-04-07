<?php

namespace App\Validators;

class ProductValidator
{
    public const rules = [
        'name' => 'required|string|max:255',
        'brand' => 'required|string|max:255',
        'description' => 'required|string|max:1024',
        'price' =>'required|numeric|min:0.01',
        'stock_quantity' =>'required|integer|min:0',
        'category_id' => 'required|integer|exists:categories,id'
    ];

    public const messages = [

        'name.required' => 'Product name is required.',
        'name.alpha' => 'Product name must only contain alphabetic characters.',
        'name.max' => 'Product name must not exceed 255 characters.',

        'brand.required' => 'Brand name is required.',
        'brand.alpha' => 'Brand name must only contain alphabetic characters.',
        'brand.max' => 'Brand name must not exceed 255 characters.',

        'description.required' => 'Product description is required.',
        'description.alpha' => 'Product description must only contain alphabetic characters.',
        'description.max' => 'Product description must not exceed 1024 characters.',

        'price.required' => 'Product price is required.',
        'price.numeric' => 'Product price must be a valid number.',
        'price.min' => 'Product price must be at least 0.01.',

        'stock_quantity.required' => 'Stock quantity is required.',
        'stock_quantity.integer' => 'Stock quantity must be an integer.',
        'stock_quantity.min' => 'Stock quantity must be at least 0.',

        'category_id.required' => 'Category ID is required.',
        'category_id.integer' => 'Category ID must be an integer.',
    ];

}
