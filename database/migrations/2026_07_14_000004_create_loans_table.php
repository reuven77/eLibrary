<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('book_id')->constrained('books')->restrictOnDelete();
            $table->timestampTz('borrowed_at');
            $table->timestampTz('due_at')->nullable();
            $table->timestampTz('returned_at')->nullable();
            $table->string('status', 40)->default('menunggu_persetujuan');
            $table->text('rejection_reason')->nullable();
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->timestampsTz();

            $table->index('user_id');
            $table->index('book_id');
            $table->index('status');
            $table->index('due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
