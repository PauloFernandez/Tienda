<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//Modelo  Valorariones
class Assessment extends Model
{
    protected $fillable = ['product_id', 'customer_id', 'order_item_id', 'qualification', 'comment', 'approved'];

    public function casts(): array
    {
        return ['approved' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer():BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order_item(): BelongsTo
    {
        return $this->belongsTo(Order_item::class);
    }
    
}
