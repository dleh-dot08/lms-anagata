<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPertemuanIdToLessonsTable extends Migration
{
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Tambahkan kolom pertemuan_id dan relasinya ke meetings.id
            $table->foreignId('pertemuan_id')->nullable()->constrained('meetings')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Drop foreign key dan kolomnya
            $table->dropForeign(['pertemuan_id']);
            $table->dropColumn('pertemuan_id');
        });
    }
}

