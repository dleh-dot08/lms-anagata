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
        Schema::table('enrollments', function (Blueprint $table) {
            // Definisikan nilai-nilai ENUM hanya 'aktif' dan 'tidak_aktif'
            $table->enum('status', ['aktif', 'tidak_aktif'])
                  ->default('aktif') // Status default saat enrollment baru dibuat adalah 'aktif'
                  ->after('tanggal_selesai'); // Sesuaikan posisi kolom jika perlu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
