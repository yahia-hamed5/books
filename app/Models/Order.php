<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_price',
        'shipping_address',
        'phone',
        'notes',
        'payment_method',
        'payment_status',
    ];

    // علاقة الطلب بصاحبه (المستخدم)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة الطلب بعناصره (سطور الفاتورة)
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
