<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();           // e.g. 'reservation_available'
            $table->string('title', 255)->nullable();        // human-readable name (Bahasa Indonesia)
            $table->string('subject', 255)->nullable();       // email subject
            $table->text('body')->nullable();                // template body with variables
            $table->string('channel', 50)->default('database'); // database, mail, whatsapp, or comma-separated
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};