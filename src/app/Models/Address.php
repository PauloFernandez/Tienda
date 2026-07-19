<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

//Modelo Direcciones
class Address extends Model
{
    protected $fillable = ['alias', 'country', 'departament', 'district', 'address', 'zip_Code', 'reference', 'default', 'customer_id'];

    public function casts(): array
    {
        return ['default' => 'boolean'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
