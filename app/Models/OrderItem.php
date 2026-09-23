<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    // علاقة السطر بالفاتورة الرئيسية
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // علاقة السطر بالكتاب
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
