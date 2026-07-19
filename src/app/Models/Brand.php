<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

//Modelo Marcas
class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'logo', 'description', 'active'];

    public function casts(): array
    {
        return [ 'active' => 'boolean' ];
    }
    
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
