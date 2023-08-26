<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'name',
        'price',
        'stock',
        'images'
    ];

    public function setImagesAttribute($images)
    {
        $this->attributes['images'] = json_encode($images ?? []);
    }

    public function getImagesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}
