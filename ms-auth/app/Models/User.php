<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens;

    public $timestamps = false;

    protected $fillable = [
        'last_name',
        'first_name',
        'email',
        'password',
        'phone_number',
        'profile_picture_url',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];
}
