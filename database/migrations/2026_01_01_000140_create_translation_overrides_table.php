<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 8)->index();
            // `site` и `admin` — словари интерфейса, `php` — группы файлов lang/
            $table->string('group')->default('site');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['locale', 'group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_overrides');
    }
};
