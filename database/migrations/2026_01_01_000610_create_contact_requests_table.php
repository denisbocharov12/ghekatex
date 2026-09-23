<?php

use App\Enums\ContactRequestSource;
use App\Enums\ContactRequestStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('country')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('source')->default(ContactRequestSource::Contacts->value)->index();
            // Заявка со страницы услуги или изделия помнит, откуда пришла
            $table->nullableMorphs('related');
            $table->string('locale', 8)->default('ro');
            $table->string('status')->default(ContactRequestStatus::New->value)->index();
            $table->string('ip_hash', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('locale', 8)->default('ro');
            $table->string('token', 64)->unique();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('contact_requests');
    }
};
