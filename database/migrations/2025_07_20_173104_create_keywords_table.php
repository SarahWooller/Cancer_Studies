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
        $table->string('keyword'); // Keyword name, not unique by itself
        $table->string('type')->nullable(); // Optional: 'category', 'subcategory', 'value'
        $table->timestamps();

        // This single line defines the 'parent_id' column
        // as UNSIGNED BIGINT and immediately sets up the foreign key constraint.
        $table->foreignId('parent_id')
              ->nullable()
              ->constrained('keywords') // Self-referencing the 'keywords' table
              ->onDelete('cascade'); // If a parent keyword is deleted, its children are too

        // Add this composite unique constraint at the end of your schema definition
        // This ensures that a keyword name is unique only when combined with its specific parent.
        $table->unique(['keyword', 'parent_id']);
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
