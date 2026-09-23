<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            // Список характеристик: [{label:{ro,en,ru}, value:{ro,en,ru}}]
            $table->json('specs')->nullable();
            $table->unsignedInteger('capacity_per_month')->nullable();
            $table->unsignedInteger('employees_count')->nullable();
            $table->string('icon')->default('factory');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
