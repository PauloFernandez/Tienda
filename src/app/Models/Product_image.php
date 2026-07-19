<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//Modelo Imagenes de producto
class Product_image extends Model
{
    protected $fillable = ['product_id', 'product_variant_id', 'url', 'order', 'is_main'];

    public function casts(): array
    {
        return ['is_main' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function product_variant(): BelongsTo
    {
        return $this->belongsTo(Product_variant::class);
    }
}
