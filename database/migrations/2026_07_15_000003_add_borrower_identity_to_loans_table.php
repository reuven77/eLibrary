<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->string('borrower_phone', 30)->nullable()->after('reviewed_by');
            $table->text('borrower_address')->nullable()->after('borrower_phone');
            $table->string('id_card_path')->nullable()->after('borrower_address');
            $table->text('borrower_notes')->nullable()->after('id_card_path');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'borrower_phone',
                'borrower_address',
                'id_card_path',
                'borrower_notes',
            ]);
        });
    }
};
