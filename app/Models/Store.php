<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $table = 'stores';
    protected $fillable = [
        'name',
        'description',
        'website',
        'email',
        'phone',
        'facebook',
        'instagram',
        'logotype',
        'isotype',
        'backdrop',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
