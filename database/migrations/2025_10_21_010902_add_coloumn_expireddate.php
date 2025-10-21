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
            $table->timestamp('payment_due_at')->nullable()->after('status');
            // Ganti status 'failed' menjadi 'expired' jika ingin lebih deskriptif, atau tambahkan.
            // Untuk saat ini kita akan tetap menggunakan 'failed' untuk booking yang hangus.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('payment_due_at');
        });
    }
};
