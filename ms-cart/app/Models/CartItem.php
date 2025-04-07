<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;


    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'name',
        'image_url'
    ];


    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
}
