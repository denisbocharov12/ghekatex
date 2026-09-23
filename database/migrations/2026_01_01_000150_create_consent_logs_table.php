<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consent_logs', function (Blueprint $table) {
            $table->id();
            // Идентификатор посетителя из cookie — персональные данные не храним
            $table->uuid('anonymous_id')->index();
            $table->string('ip_hash', 64)->nullable();
            $table->json('categories');
            $table->string('policy_version', 16);
            $table->string('locale', 8)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_logs');
    }
};
