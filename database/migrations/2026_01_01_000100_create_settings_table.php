<?php

use App\Enums\SettingGroup;
use App\Enums\SettingType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group')->default(SettingGroup::General->value)->index();
            $table->string('type')->default(SettingType::String->value);
            // Переводимое значение хранится как {"ro": ..., "en": ...}, обычное — как {"value": ...}
            $table->json('value')->nullable();
            $table->boolean('is_translatable')->default(false);
            $table->string('label')->nullable();
            $table->string('hint')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
