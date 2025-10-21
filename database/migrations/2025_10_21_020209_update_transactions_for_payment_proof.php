<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Menambah kolom untuk menyimpan path bukti pembayaran
            $table->string('payment_proof')->nullable()->after('payment_method');
        });

        // Mengubah tipe kolom ENUM untuk menambahkan status baru
        // Perlu menggunakan DB::statement untuk memodifikasi ENUM di MySQL
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'paid', 'failed', 'waiting_confirmation') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan ENUM ke kondisi semula
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending'");

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('payment_proof');
        });
    }
};
