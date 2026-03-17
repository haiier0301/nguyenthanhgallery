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
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('code', 20);
            $table->string('title')->nullable();
            $table->string('series_year', 4)->nullable();
            $table->string('medium');
            $table->string('size', 100)->nullable();
            $table->string('image_path');
            $table->integer('year')->nullable();
            $table->boolean('available')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index('artist_id');
            $table->index('series_year');
            $table->index('available');
            $table->index(['artist_id', 'series_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};
