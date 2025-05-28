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
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('enrollment_id')->nullable()->after('id');
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('enrollment_id');
        });
    }
    
};
