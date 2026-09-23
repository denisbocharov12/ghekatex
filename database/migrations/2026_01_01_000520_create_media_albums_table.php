<?php

use App\Enums\MediaItemType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_albums', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->nullable()->constrained('media_albums')->cascadeOnDelete();
            $table->string('type')->default(MediaItemType::Image->value)->index();
            $table->json('title')->nullable();
            $table->json('caption')->nullable();
            // Для видео: провайдер и ссылка; для локального файла — медиа-коллекция `file`
            $table->string('video_provider')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_items');
        Schema::dropIfExists('media_albums');
    }
};
