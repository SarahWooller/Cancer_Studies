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
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->unique(); // The actual keyword name (e.g., "Breast", "Adherent")
            $table->foreignId('parent_id')->nullable()->constrained('keywords')->onDelete('cascade'); // Self-referencing foreign key for hierarchy
            $table->string('type')->nullable(); // Optional: 'category', 'subcategory', 'value'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};
