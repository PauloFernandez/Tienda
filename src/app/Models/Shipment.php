<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//Modelo Envios
class Shipment extends Model
{
    protected $fillable = ['order_id', 'transporters', 'tracking_number', 'state', 'shipping_date', 'estimated_delivery_date', 'actual_delivery_date'];

    public function casts(): array
    {
        return [
            'shipping_date' => 'dateTime',
            'estimated_delivery_date' => 'dateTime',
            'actual_delivery_date' => 'dateTime',
            ];
    }
    
    public function order():BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
