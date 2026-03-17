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
        Schema::create('exhibitions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['solo', 'group', 'award', 'art-fair']);
            $table->string('year', 4);
            $table->string('title');
            $table->string('location');
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index('type');
            $table->index('year');
            $table->index(['year', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibitions');
    }
};
