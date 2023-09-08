<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGender extends Model
{
    use HasFactory;
    protected $table = 'product_genders';
    protected $fillable = [
        'product_id',
        'gender_id'
    ];
}
