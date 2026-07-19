<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

//Modelo Color
class Color extends Model
{
    protected $fillable = ['name', 'code_hex'];

    public function product_variants(): HasMany
    {
        return $this->hasMany(Product_variant::class);
    }
}
