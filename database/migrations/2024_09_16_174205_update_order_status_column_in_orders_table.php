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
        Schema::table('orders', function (Blueprint $table) {
            // Change the 'status' enum and replace 'canceled' with 'cancelled'
            $table->enum('status', ['new', 'processing', 'shipped', 'delivered', 'cancelled'])
                ->default('new')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert back to the old status with 'canceled'
            $table->enum('status', ['new', 'processing', 'shipped', 'delivered', 'canceled'])
                ->default('new')
                ->change();
        });
    }
};
