<?php

use App\Enums\PageTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            // Слаг общий для всех локалей: путь и так содержит языковой префикс
            $table->string('slug')->unique();
            $table->string('template')->default(PageTemplate::Default->value);
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->json('body')->nullable();
            // Конструктор секций: [{type, data:{...переводы внутри}}]
            $table->json('blocks')->nullable();
            // Системную страницу нельзя удалить, слаг у неё неизменяем
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
