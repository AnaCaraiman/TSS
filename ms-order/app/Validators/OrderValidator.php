<?php

namespace App\Validators;

use App\Enums\PaymentType;
use Illuminate\Validation\Rule;

class OrderValidator
{
    public static function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'number' => 'required|integer|max:255',
            'additional_info' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'county' => 'required|string|max:255',
            'postcode' => 'required|integer',
            'payment_type' => ['required', Rule::in(self::getPaymentTypes())],
            'price' => 'required|numeric',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer',
            'products.*.name' => 'required|string|max:255',
            'products.*.price' => 'required|numeric',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.image_url' => 'nullable|string|max:255',
        ];
    }

    public static function messages(): array
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.integer' => 'The user ID must be a valid integer.',
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name cannot exceed 255 characters.',
            'street.required' => 'The street is required.',
            'street.string' => 'The street must be a valid string.',
            'street.max' => 'The street cannot exceed 255 characters.',
            'number.required' => 'The number is required.',
            'number.integer' => 'The number must be a valid integer.',
            'number.max' => 'The number cannot exceed 255.',
            'additional_info.string' => 'Additional info must be a valid string.',
            'additional_info.max' => 'Additional info cannot exceed 255 characters.',
            'city.required' => 'The city is required.',
            'city.string' => 'The city must be a valid string.',
            'city.max' => 'The city cannot exceed 255 characters.',
            'county.required' => 'The county is required.',
            'county.string' => 'The county must be a valid string.',
            'county.max' => 'The county cannot exceed 255 characters.',
            'postal_code.required' => 'The postal code is required.',
            'postal_code.integer' => 'The postal code must be a valid integer.',
            'postal_code.max' => 'The postal code cannot exceed 255.',
            'payment_type.required' => 'The payment type is required.',
            'payment_type.in' => 'The selected payment type is invalid. Choose from: ' . implode(', ', self::getPaymentTypes()),
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a valid numeric.',
            'products.required' => 'At least one product is required.',
            'products.array' => 'Products must be an array.',
            'products.*.id.required' => 'The product ID is required.',
            'products.*.id.integer' => 'The product ID must be a valid integer.',
            'products.*.name.required' => 'Each product must have a name.',
            'products.*.price.required' => 'Each product must have a price.',
            'products.*.quantity.required' => 'Each product must have a quantity.',
            'products.*.quantity.integer' => 'Product quantity must be an integer.',
            'products.*.quantity.min' => 'Each product must have at least 1 item.',
        ];
    }

    private static function getPaymentTypes(): array
    {
        return array_column(PaymentType::cases(), 'value');
    }
}
