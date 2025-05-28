<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('kelas_id')->nullable()->after('jenjang_id');
    
            // Tambahkan foreign key constraint
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('set null');
            // Gunakan onDelete('set null') karena kolomnya nullable, artinya jika data pada tabel kelas dihapus, maka kelas_id menjadi null
        });
    }
    
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu sebelum menghapus kolom
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
    }
    
};
