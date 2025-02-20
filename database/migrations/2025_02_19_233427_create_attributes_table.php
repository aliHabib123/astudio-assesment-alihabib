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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique(); // For programmatic access
            $table->enum('type', ['text', 'date', 'number', 'select']);
            $table->json('options')->nullable(); // For select type, stores possible values
            $table->string('default_value')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Index for common queries
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
