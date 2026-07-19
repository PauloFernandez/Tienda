<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

//Modelo  Producto variante
class Product_variant extends Model
{
    protected $fillable = ['product_id', 'size_id', 'color_id', 'sku', 'stock', 'additional_price', 'active'];

    public function casts(): array
    {
        return [
            'additional_price' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function product_images(): HasMany
    {
        return $this->hasMany(Product_image::class, 'product_variant_id');
    }

    public function Shopping_cart_items(): HasMany
    {
        return $this->hasMany(Shopping_cart_item::class, 'product_variant_id');
    }

    public function order_items():HasMany
    {
        return $this->hasMany(Order_item::class, 'product_variant_id');
    }

    public function image_main()
    {
        //1. Busca una imagen marcada como principal para esta variante
        //2. Si no existe, cae a la imagen principal del producto en general
        return $this->product_images()->where('is_main', true)->first() ??
               $this->product->product_images()->whereNull('product_variant_id')
                                               ->where('is_main', true)->first();
    }

    //Calculo del precio final

}
