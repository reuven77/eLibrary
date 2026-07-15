<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 500);
            $table->string('isbn', 20)->nullable()->unique();
            $table->foreignUuid('author_id')->constrained('authors')->restrictOnDelete();
            $table->foreignUuid('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('cover_image_path', 500)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->enum('format', ['fisik', 'digital', 'keduanya']);
            $table->unsignedInteger('stock')->default(0);
            $table->text('synopsis')->nullable();
            $table->unsignedSmallInteger('published_year')->nullable();
            $table->string('call_number', 50);
            $table->timestampsTz();

            $table->index('category_id');
            $table->index('author_id');
            $table->index('call_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
