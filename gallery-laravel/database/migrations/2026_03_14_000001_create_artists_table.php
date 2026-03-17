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
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_display');
            $table->string('code', 10)->unique();
            $table->string('slug')->unique();
            $table->date('born')->nullable();
            $table->string('birth_place')->nullable();
            $table->text('bio')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('has_series')->default(false);
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('slug');
            $table->index('code');
            $table->index('featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
