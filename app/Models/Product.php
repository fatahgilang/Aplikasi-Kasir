<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stock',
    ];

    // relasi table product dengan  table order_details
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}


