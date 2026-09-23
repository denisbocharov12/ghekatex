<?php

use App\Enums\OfficeType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default(OfficeType::Office->value)->index();
            $table->json('name');
            $table->json('address');
            $table->json('city')->nullable();
            $table->string('country_code', 2)->default('MD');
            $table->string('postal_code', 16)->nullable();
            $table->json('phones')->nullable();
            $table->json('emails')->nullable();
            $table->json('working_hours')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            // Главный адрес попадает в микроразметку и в футер
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
