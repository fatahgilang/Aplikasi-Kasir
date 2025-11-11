<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
     // relasi table order dengan  table customer
    public function customer():BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected $fillable = [
        'customer_id',
        'date',
        'total_price',
        'discount',
        'discount_amount',
        'total_payment',
        'status',
    ];

    // relasi table product dengan  table order_details
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
