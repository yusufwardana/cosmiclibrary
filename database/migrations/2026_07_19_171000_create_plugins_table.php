<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugins', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('name', 200);
            $table->string('version', 20)->default('1.0.0');
            $table->string('description')->nullable();
            $table->string('author')->nullable();
            $table->string('hook', 100)->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(false);
            $table->integer('priority')->default(100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugins');
    }
};