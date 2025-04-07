<?php

namespace App\Validators;

class RegisterValidators
{
    public const rules = [
//        'last_name' => 'required|alpha|max:255',
//        'first_name' => 'required|alpha|max:255',
//        'phone_number' => ['required', 'regex:/^(0|\+40)[0-9]{9}$/'],
//        'profile_picture_url' => 'string',
        'email' => 'required|email|max:255|unique:users',
//        'password' => 'required|string|confirmed',
    ];

    public const messages = [
//        'last_name.required' => 'Last name is required.',
//        'last_name.alpha' => 'Invalid last name.',
//        'last_name.max' => 'Last name must not exceed 255 characters.',
//
//        'first_name.required' => 'First name is required.',
//        'first_name.alpha' => 'Invalid first name.',
//        'first_name.max' => 'First name must not exceed 255 characters.',
//
//        'phone_number.required' => 'Phone number is required.',
//        'phone_number.regex' => 'Invalid phone number.',
//
//        'profile_picture_url.string' => 'Profile picture URL must be a valid string.',

        'email.required' => 'Email address is required.',
        'email.email' => 'Email address must be a valid email format.',
        'email.max' => 'Email address must not exceed 255 characters.',
        'email.unique' => 'Email address is already taken.',

//        'password.required' => 'Password is required.',
//        'password.string' => 'Password must be a string.',
//        'password.confirmed' => 'Password confirmation does not match.',
    ];

}
