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
}
