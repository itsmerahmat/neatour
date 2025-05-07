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
        Schema::create('destination_category', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('destination_id')->constrained('destinations');
            $table->foreignId('category_id')->constrained('categories');
            $table->timestamps();
            
            // Create a unique constraint on the combination of destination_id and category_id
            $table->unique(['destination_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destination_category');
    }
};
