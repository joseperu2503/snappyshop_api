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
        'free_shipping'
    ];

    protected $casts = [
        'free_shipping' => 'boolean', // Se convierte en un tipo 'tinyint(1)' en la base de datos para insertarlo y se recupera como booleano en consultas (MySQL).
        'images' => 'array', // Se codifica como JSON para insertarlo en la base de datos y se decodifica al consultar la base de datos. En casos más complejos, se pueden usar mutadores y accesores.
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // Establecer el valor predeterminado de 'free_shipping' en false si no se recibe en la solicitud
            if (!isset($product->free_shipping)) {
                $product->free_shipping = false;
            }

            // Establecer el valor predeterminado de 'images' en un array vacío si no se recibe en la solicitud
            if (!isset($product->images)) {
                $product->images = [];
            }

            // Establecer el valor predeterminado de 'colors' en un array vacío si no se recibe en la solicitud
            if (!isset($product->colors)) {
                $product->colors = [];
            }
        });
    }

    /*
        Mutators:

        Los mutadores son funciones que permiten modificar el valor de un atributo
        antes de que se almacene en la base de datos. Estas funciones se utilizan
         principalmente para realizar transformaciones en los datos antes
         de su persistencia.
    */
    public function setColorsAttribute($colors)
    {
        $this->attributes['colors'] = json_encode($colors);
    }

    /*
        Accessor:

        Los accesores son funciones que permiten modificar el valor de un
        atributo antes de que se devuelva al acceder a él en el modelo.
        Estas funciones se utilizan principalmente para formatear o modificar
        la representación de datos al recuperarla del modelo.
    */
    public function getColorsAttribute($value)
    {
        return json_decode($value, true);
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
