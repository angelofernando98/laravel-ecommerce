<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'images', 'description', 'price', 'is_active', 'is_featured', 'in_stock', 'on_sale'
    ];

    // The images attribute is a JSON field that stores the image paths.
    // We cast it to an array so we can easily access the image paths
    // in our code.
    protected $casts = [
        'images' => 'array',
    ];

    // Get the category that owns the Product
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Get the brand that owns the Product
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Get all of the order items for the Product
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

?>