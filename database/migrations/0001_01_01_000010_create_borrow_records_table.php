<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrow_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained();
            $table->foreignId('book_item_id')->constrained();
            $table->foreignId('librarian_out_id')->constrained('users');
            $table->foreignId('librarian_in_id')->nullable()->constrained('users');
            $table->date('borrow_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->tinyInteger('extend_count')->unsigned()->default(0);
            $table->enum('status', ['borrowed', 'returned', 'overdue', 'lost'])->default('borrowed');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('member_id');
            $table->index('book_item_id');
            $table->index('status');
            $table->index('borrow_date');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrow_records');
    }
};
