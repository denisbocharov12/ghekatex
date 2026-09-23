<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advantages', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->default('sparkles');
            $table->json('title');
            $table->json('description')->nullable();
            // Числовой акцент счётчика и подпись к нему по локалям
            $table->string('value')->nullable();
            $table->json('value_suffix')->nullable();
            $table->boolean('is_counter')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advantages');
    }
};
