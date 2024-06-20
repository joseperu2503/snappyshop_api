<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'total_amount',
        'shipping_fee',
        'card_number',
        'card_holder_name',
        'order_status_id',
        'address_id',
        'payment_method_id',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_order', 'order_id', 'product_id');
    }

    public function product_orders()
    {
        return $this->hasMany(ProductOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function order_status()
    {
        return $this->belongsTo(OrderStatus::class);
    }
}
