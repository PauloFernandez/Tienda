<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//Modelo Pagos
class Payment extends Model
{
    protected $fillable = ['order_id', 'payment_method_id', 'amount', 'state', 'transaction_reference', 'paid_at'];

    public function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'dateTime',
        ];
    }

    public function oredr(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payment_method(): BelongsTo
    {
        return $this->belongsTo(Payment_method::class);
    }
}
