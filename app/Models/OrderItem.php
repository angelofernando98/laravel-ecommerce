<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'quantity', 'unit_amount', 'total_amount'];

    // Get the order that owns the OrderItem
    public function order(){
        return $this->belongsTo(Order::class);
    }

    // Get the product that owns the OrderItem
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
