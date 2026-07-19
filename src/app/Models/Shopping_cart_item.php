<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//Modelo Carrito de Compras Item

class Shopping_cart_item extends Model
{
    protected $fillable = ['shopping_cart_id', 'product_variant_id', 'quantity', 'unit_price'];

    public function casts(): array
    {
        return ['unit_price' => 'decimal:2'];
    }

    public function shopping_cart(): BelongsTo
    {
        return $this->belongsTo(Shopping_cart::class);
    }

    public function product_variant(): BelongsTo
    {
        return $this->belongsTo(Product_variant::class);
    }
}
