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
        // Create the categories table
        // This table stores the categories of products.
        // Each category has a name, a slug, an image, a boolean indicating if it is active
        // The timestamps (created_at and updated_at).
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table ->string('slug')->unique();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
