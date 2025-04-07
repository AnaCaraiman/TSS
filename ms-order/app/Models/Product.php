<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'product_id',
        'name',
        'price',
        'image_url'
    ];

    protected $hidden = [
        'id'
    ];



    public function orders()
    {
        return $this->belongsToMany(Order::class)->withTimestamps()->withPivot('quantity');
    }

}
