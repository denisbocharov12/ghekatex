<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('article')->nullable()->index();
            $table->json('name');
            $table->json('short_description')->nullable();
            $table->json('description')->nullable();
            $table->json('composition')->nullable();
            // Характеристики изделия: [{label:{...}, value:{...}}]
            $table->json('attributes')->nullable();
            $table->unsignedInteger('min_order_quantity')->nullable();
            $table->unsignedSmallInteger('lead_time_days')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('fabric_product', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fabric_id')->constrained()->cascadeOnDelete();
            $table->primary(['fabric_id', 'product_id']);
        });

        Schema::create('product_treatment', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('treatment_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'treatment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_treatment');
        Schema::dropIfExists('fabric_product');
        Schema::dropIfExists('products');
    }
};
