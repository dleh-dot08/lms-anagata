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
        // Langkah 1: Tambahkan kolom meeting_id dulu, nullable untuk mencegah error jika ada data lama
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('meeting_id')->nullable()->after('course_id');
        });

        // Langkah 2: Tambahkan foreign key dan unique constraint
        Schema::table('attendances', function (Blueprint $table) {
            // Foreign key ke tabel meetings
            $table->foreign('meeting_id')
                  ->references('id')->on('meetings')
                  ->onDelete('cascade');

            // Unique constraint agar user hanya bisa absen 1x per meeting
            $table->unique(['meeting_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Hapus unique constraint
            $table->dropUnique(['meeting_id', 'user_id']);

            // Hapus foreign key constraint
            $table->dropForeign(['meeting_id']);

            // Hapus kolom meeting_id
            $table->dropColumn('meeting_id');
        });
    }
};
