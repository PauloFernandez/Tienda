<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

//Modelo Producto
class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_id', 'brand_id', 'name', 'slug', 'short_description', 'description', 'base_price', 'offer_price', 'gender', 
                            'material', 'outstanding', 'active', 'meta_title', 'meta_description'];

     public function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'offer_price' => 'decimal:2',
            'outstanding' => 'boolean',
            'active' => 'boolean',
            ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function product_variants(): HasMany
    {
        return $this->hasMany(Product_variant::class);
    }

    public function product_images(): HasMany
    {
        return $this->hasMany(Product_image::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'favorites');
    }
    
}
