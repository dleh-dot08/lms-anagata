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
            // Asumsi user/peserta memiliki relasi dengan jenjang, sekolah, dan kelas
            $table->foreignId('jenjang_id')->nullable()->constrained('jenjang')->onDelete('set null');
            $table->foreignId('sekolah_id')->nullable()->constrained('sekolah')->onDelete('set null');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null'); // Asumsi nama tabel adalah 'kelas'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('jenjang_id');
            $table->dropConstrainedForeignId('sekolah_id');
            $table->dropConstrainedForeignId('kelas_id');
        });
    }
};
