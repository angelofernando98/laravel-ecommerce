<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'grand_total', 'payment_method', 'payment_status', 'status', 'currency', 'shipping_amount', 'shipping_method', 'notes'];

    // Get the user that owns the Order
    public function user(){
        return $this->belongsTo(User::class);
    }

    // Get all of the order items for the Order
    public function Items(){
        return $this->hasMany(OrderItem::class);
    }

    // Get the address that owns the Order
    public function address(){
        return $this->belongsTo(Address::class);
    }
}
