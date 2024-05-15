<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $table = 'addresses';
    protected $fillable = [
        'address',
        'detail',
        'recipient_name',
        'phone',
        'references',
        'is_active',
        'user_id',
        'latitude',
        'longitude',
        'primary',
    ];

    protected $casts = [
        'primary' => 'boolean', // Se convierte en un tipo 'tinyint(1)' en la base de datos para insertarlo y se recupera como booleano en consultas (MySQL).
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
