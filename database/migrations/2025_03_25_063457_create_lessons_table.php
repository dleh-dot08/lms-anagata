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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('judul');
            $table->text('konten')->nullable();
            $table->string('video_url1')->nullable();
            $table->string('video_url2')->nullable();
            $table->string('video_url3')->nullable();
            $table->string('file_materi1')->nullable();
            $table->string('file_materi2')->nullable();
            $table->string('file_materi3')->nullable();
            $table->string('file_materi4')->nullable();
            $table->string('file_materi5')->nullable();
            $table->integer('pertemuan_ke');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
