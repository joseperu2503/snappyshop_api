<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, Searchable;
    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'images',
        'store_id',
        'category_id',
        'colors',
        'discount',
        'is_active'
    ];

    protected $casts = [
        'images' => 'array', // Se codifica como JSON para insertarlo en la base de datos y se decodifica al consultar la base de datos. En casos más complejos, se pueden usar mutadores y accesores.
        'is_active' => 'bool',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
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

    /**
     * Obtener el precio de venta con descuento.
     *
     * @return float
     */
    public function getSalePriceAttribute()
    {
        $sale_price = $this->price;
        if ($this->discount) {
            $sale_price = round($this->price * (1 - $this->discount / 100), 2);
        }
        return $sale_price;
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_size', 'product_id', 'size_id');
    }

    public function genders()
    {
        return $this->belongsToMany(Gender::class, 'product_gender', 'product_id', 'gender_id');
    }

    public function product_sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function product_genders()
    {
        return $this->hasMany(ProductGender::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'product_order', 'product_id', 'order_id');
    }
}
