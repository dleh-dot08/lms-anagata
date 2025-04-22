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
        // Menambahkan kolom activity_id
        $table->unsignedBigInteger('activity_id')->nullable()->after('course_id');
        
        // Menambahkan foreign key yang mengacu ke tabel activities
        $table->foreign('activity_id')->references('id')->on('activities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
                    // Menghapus foreign key
        $table->dropForeign(['activity_id']);
        
        // Menghapus kolom activity_id
        $table->dropColumn('activity_id');
        });
    }
};
