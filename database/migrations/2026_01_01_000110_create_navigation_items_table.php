<?php

use App\Enums\NavigationMenu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('menu')->default(NavigationMenu::Header->value)->index();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->nullOnDelete();
            $table->json('label');
            // Либо именованный маршрут с параметрами, либо произвольный URL
            $table->string('route_name')->nullable();
            $table->json('route_params')->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('opens_in_new_tab')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
