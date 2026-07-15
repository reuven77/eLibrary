<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrasi skema lama → approval flow (aman untuk DB yang sudah jalan).
     * Instalasi baru sudah mendapat skema final di create_loans_table.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE loans DROP CONSTRAINT IF EXISTS loans_status_check');
            DB::statement('ALTER TABLE loans ALTER COLUMN due_at DROP NOT NULL');
            DB::statement('ALTER TABLE loans ALTER COLUMN status TYPE varchar(40)');
            DB::statement("ALTER TABLE loans ALTER COLUMN status SET DEFAULT 'menunggu_persetujuan'");
        }

        if (Schema::hasColumn('loans', 'status')) {
            DB::table('loans')->where('status', 'dipinjam')->update(['status' => 'disetujui']);
            DB::table('loans')->where('status', 'hilang')->update(['status' => 'ditolak']);
        }

        Schema::table('loans', function (Blueprint $table) {
            if (! Schema::hasColumn('loans', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
            if (! Schema::hasColumn('loans', 'reviewed_by')) {
                $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        if ($driver === 'pgsql') {
            // Pastikan hanya satu CHECK status yang aktif.
            DB::statement('ALTER TABLE loans DROP CONSTRAINT IF EXISTS loans_status_check');
            DB::statement("ALTER TABLE loans ADD CONSTRAINT loans_status_check CHECK (status IN ('menunggu_persetujuan', 'disetujui', 'ditolak', 'dikembalikan', 'terlambat'))");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE loans DROP CONSTRAINT IF EXISTS loans_status_check');
        }

        DB::table('loans')->where('status', 'disetujui')->update(['status' => 'dipinjam']);
        DB::table('loans')->whereIn('status', ['menunggu_persetujuan', 'ditolak'])->update(['status' => 'dikembalikan']);

        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'reviewed_by')) {
                $table->dropConstrainedForeignId('reviewed_by');
            }
            if (Schema::hasColumn('loans', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });

        if ($driver === 'pgsql') {
            DB::statement('UPDATE loans SET due_at = borrowed_at WHERE due_at IS NULL');
            DB::statement('ALTER TABLE loans ALTER COLUMN due_at SET NOT NULL');
            DB::statement("ALTER TABLE loans ALTER COLUMN status SET DEFAULT 'dipinjam'");
            DB::statement("ALTER TABLE loans ADD CONSTRAINT loans_status_check CHECK (status IN ('dipinjam', 'dikembalikan', 'terlambat', 'hilang'))");
        }
    }
};
