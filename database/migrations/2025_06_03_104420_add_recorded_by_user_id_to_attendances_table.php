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
        Schema::table('attendances', function (Blueprint $table) {
            // Kolom baru untuk menyimpan ID mentor yang merekam absensi
            $table->foreignId('recorded_by_user_id')
                  ->nullable() // Bisa null jika absensi lama tidak punya data ini
                  ->constrained('users') // Merujuk ke tabel 'users'
                  ->onDelete('set null') // Jika user mentor dihapus, set ID ini menjadi null
                  ->after('user_id'); // Posisikan setelah user_id (siswa)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['recorded_by_user_id']); // Hapus foreign key
            $table->dropColumn('recorded_by_user_id'); // Hapus kolom
        });
    }
};