<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('member_number')->comment('NISN for students, NIP for teachers');
            $table->enum('type', ['student', 'teacher', 'staff']);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('class_name')->nullable()->comment('Class for students (e.g. X IPA 1)');
            $table->date('join_date')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'suspended', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('member_number');
            $table->index('type');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
