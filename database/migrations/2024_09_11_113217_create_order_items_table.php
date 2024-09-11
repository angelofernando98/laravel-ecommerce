<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the order items table
        // Each order item has an id, an order id (foreign key to the orders table), a product id (foreign key to the products table), a quantity, a unit amount, a total amount, and timestamps (created_at and updated_at)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreingId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreingId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_amount', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
