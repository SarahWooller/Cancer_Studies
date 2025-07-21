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
        Schema::create('keyword_study', function (Blueprint $table) {
            // Using foreignId for cleaner syntax with foreign key constraints
            $table->foreignId('keyword_id')->constrained()->onDelete('cascade');
            $table->foreignId('study_id')->constrained()->onDelete('cascade');

            // Define primary key on both columns to ensure unique combinations
            $table->primary(['keyword_id', 'study_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keyword_study');
    }
};
