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
        // Menggunakan DB::statement untuk mengubah tipe ENUM
        DB::statement("ALTER TABLE bookings CHANGE status status ENUM('pending','approved','cancelled','completed','expired') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback ke state sebelumnya
        DB::statement("ALTER TABLE bookings CHANGE status status ENUM('pending','approved','cancelled','completed') NOT NULL DEFAULT 'pending'");
    }
};
