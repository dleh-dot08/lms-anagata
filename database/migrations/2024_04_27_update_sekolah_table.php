<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add missing columns to sekolah table
        Schema::table('sekolah', function (Blueprint $table) {
            if (!Schema::hasColumn('sekolah', 'jenjang_id')) {
                $table->foreignId('jenjang_id')->after('alamat')->constrained('jenjang');
            }
            if (!Schema::hasColumn('sekolah', 'created_at')) {
                $table->timestamps();
            }
            if (!Schema::hasColumn('sekolah', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Add sekolah_id to users table if it doesn't exist
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sekolah_id')) {
                $table->foreignId('sekolah_id')->nullable()->constrained('sekolah')->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sekolah_id']);
            $table->dropColumn('sekolah_id');
        });

        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropForeign(['jenjang_id']);
            $table->dropColumn(['jenjang_id', 'created_at', 'updated_at', 'deleted_at']);
        });
    }
}; 