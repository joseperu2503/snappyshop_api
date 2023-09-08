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
        'description',
        'price',
        'stock',
        'images',
        'user_id',
        'brand_id',
        'category_id',
        'colors',
        'is_public'
    ];

    public function setImagesAttribute($images)
    {
        $this->attributes['images'] = json_encode($images ?? []);
    }

    public function getImagesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setColorsAttribute($colors)
    {
        $this->attributes['colors'] = json_encode($colors ?? []);
    }

    public function getColorsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_sizes', 'product_id', 'size_id');
    }

    public function genders()
    {
        return $this->belongsToMany(Gender::class, 'product_genders', 'product_id', 'gender_id');
    }

    public function product_sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function product_genders()
    {
        return $this->hasMany(ProductGender::class);
    }
}
