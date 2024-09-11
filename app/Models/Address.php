<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'first_name', 'last_name', 'phone', 'street_address', 'city', 'state', 'zip_code'];

    // Get the order that owns the Address
    public function order(){
        return $this->belongsTo(Order::class);
    }

    // Get the full address of the user, which is the first name and last name concatenated with a space in between.
    // Example: John Doe
    public function getFullAddressAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
