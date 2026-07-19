<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

//Modelo Talla
class Size extends Model
{
    protected $fillable = ['name', 'order'];

    public function product_variants(): HasMany
    {
        return $this->hasMany(Product_variant::class);
    }   
}
