<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('sekolah')) {
            Schema::create('sekolah', function (Blueprint $table) {
                $table->id();
                $table->string('nama_sekolah');
                $table->text('alamat');
                $table->foreignId('jenjang_id')->constrained('jenjang');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Add sekolah_id to users table if it doesn't exist
        if (!Schema::hasColumn('users', 'sekolah_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('sekolah_id')->nullable()->constrained('sekolah')->nullOnDelete();
            });
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sekolah_id')) {
                $table->dropForeign(['sekolah_id']);
                $table->dropColumn('sekolah_id');
            }
        });

        Schema::dropIfExists('sekolah');
    }
}; 