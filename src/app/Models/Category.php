<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

//Modelo Catagoria
class Category extends Model
{
    protected $fillable = ['category_parent_id', 'name', 'slug', 'escription', 'image', 'order', 'active'];

    public function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function category_parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_parent_id');
    }

    public function subcategoryes(): HasMany
    {
        return $this->hasMany(Category::class, 'category_parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
}
