<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('isbn', 20)->nullable()->unique();
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->smallInteger('publish_year')->unsigned()->nullable();
            $table->string('edition')->nullable();
            $table->string('language', 50)->default('Indonesia');
            $table->unsignedInteger('pages')->nullable();
            $table->string('ddc_classification', 20)->nullable()->comment('Dewey Decimal');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->unsignedInteger('total_copies')->default(0);
            $table->unsignedInteger('available_copies')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('title');
            $table->index('author');
            $table->index('category_id');
            $table->index('ddc_classification');
            $table->index('deleted_at');

            // ponytail: SQLite (test) tidak support fulltext; skip pada driver non-MySQL
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->fullText(['title', 'author', 'description']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
