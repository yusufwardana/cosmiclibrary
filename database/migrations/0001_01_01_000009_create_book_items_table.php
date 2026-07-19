<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('barcode', 100)->unique()->comment('individual asset tag barcode');
            $table->string('call_number', 50)->nullable()->comment('shelf classification number');
            $table->string('shelf_location', 100)->nullable();
            $table->date('acquisition_date')->nullable();
            $table->string('acquisition_source', 100)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->enum('condition', ['good', 'fair', 'damaged', 'lost'])->default('good');
            $table->enum('status', ['available', 'borrowed', 'reserved', 'maintenance', 'lost', 'disposed'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('book_id');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_items');
    }
};
