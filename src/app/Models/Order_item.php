<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

//Modelo Pedidos item
class Order_item extends Model
{
    protected $fillable = ['order_id', 'product_variant_id', 'product_name', 'size', 'color', 'sku', 'quantity', 'unit_price', 'subtotal'];

     public function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            ];
    }

    public function oredr(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product_variant(): BelongsTo
    {
        return $this->belongsTo(Product_variant::class);
    }

    public function assessments(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }
}
