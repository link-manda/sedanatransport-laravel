<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tambahkan kolom setelah kolom 'status' atau sesuai preferensi Anda
            $table->text('rejection_reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Pastikan kolom bisa dihapus jika rollback
            if (Schema::hasColumn('transactions', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
