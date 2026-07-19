<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

//Modelo Carrito de Compras
class Shopping_cart extends Model
{
    protected $fillable = ['customer_id', 'session_id'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Shopping_cart_item::class);
    }

    public function total(): float
    {
        return $this->items->sum(fn ($item) => $item->quantity * $item->unit_price);
    }
}
