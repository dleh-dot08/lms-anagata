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
        Schema::table('meetings', function (Blueprint $table) {
            // Tambahkan kolom untuk jam mulai dan jam selesai jika belum ada
            // Asumsi ini belum ada di tabel meetings Anda saat ini
            $table->time('jam_mulai')->nullable()->after('tanggal_pelaksanaan');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');

            // Kolom JSON untuk menyimpan riwayat perubahan jadwal
            $table->json('schedule_history')->nullable()->after('jam_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn('schedule_history');
            $table->dropColumn('jam_selai'); // Hapus juga kolom jam jika ditambahkan di up()
            $table->dropColumn('jam_mulai'); // Hapus juga kolom jam jika ditambahkan di up()
        });
    }
};
