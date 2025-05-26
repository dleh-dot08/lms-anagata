<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom enrollment_id yang nullable dulu
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('enrollment_id')->nullable()->after('id');
        });

        // 2. Isi kolom enrollment_id berdasarkan relasi user_id & course_id
        DB::table('attendances')->get()->each(function ($attendance) {
            $enrollment = DB::table('enrollments')
                ->where('user_id', $attendance->user_id)
                ->where('course_id', $attendance->course_id)
                ->first();

            if ($enrollment) {
                DB::table('attendances')
                    ->where('id', $attendance->id)
                    ->update(['enrollment_id' => $enrollment->id]);
            }
        });

        // 3. Ubah kolom enrollment_id menjadi NOT NULL (optional)
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('enrollment_id')->nullable()->change();
        });

        // 4. Tambahkan foreign key constraint
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('enrollment_id')
                  ->references('id')
                  ->on('enrollments')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
            $table->dropColumn('enrollment_id');
        });
    }
};
