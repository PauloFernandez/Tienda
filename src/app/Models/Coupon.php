<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

//Modelo Cupon
class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'min_amount', 'use_max', 'current_use', 'start_date', 'end_date', 'active'];

    public function casts(): array
    {
        return [
            'start_date' => 'dateTime',
            'end_date' => 'dateTime',
            'active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
