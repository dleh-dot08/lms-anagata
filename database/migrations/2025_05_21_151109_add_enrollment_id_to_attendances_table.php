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
        Schema::table('attendances', function (Blueprint $table) {
            // Add enrollment_id as nullable
            $table->foreignId('enrollment_id')->nullable()->after('id');
        });

        // Update existing records to set enrollment_id based on user_id and course_id
        DB::statement('
            UPDATE attendances a
            LEFT JOIN enrollments e ON a.user_id = e.user_id AND a.course_id = e.course_id
            SET a.enrollment_id = e.id
            WHERE a.deleted_at IS NULL
        ');

        Schema::table('attendances', function (Blueprint $table) {
            // Add the foreign key constraint but keep it nullable
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