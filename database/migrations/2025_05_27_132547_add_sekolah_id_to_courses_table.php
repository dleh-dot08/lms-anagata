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
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('sekolah_id')->nullable()->after('mentor_id'); // sesuaikan posisinya
        });
    }
    
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('sekolah_id');
        });
    }
    
};
