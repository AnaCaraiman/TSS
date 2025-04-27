<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'favorite_id',
        'product_id',
        'quantity',
        'name',
        'image_url',
        'price',
    ];

    public function favorite()
    {
        return $this->belongsTo(Favorite::class);
    }
}
