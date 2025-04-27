<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'id',
        'name',
        'street',
        'number',
        'additional_info',
        'city',
        'county',
        'postcode',
        'price',
        'user_id',
        'price',
        'status',
        'payment_type'
    ];

    protected $hidden = [
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'payment_type' => PaymentType::class,
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['external_product_id', 'quantity'])
            ->withTimestamps();
    }

}
