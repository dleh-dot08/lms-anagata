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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_kelas');
            $table->text('deskripsi')->nullable();
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');
            $table->foreignId('jenjang_id')->constrained('jenjang')->onDelete('cascade');
            $table->enum('level', ['Beginner', 'Intermediate', 'Advanced']);
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->date('waktu_mulai');
            $table->date('waktu_akhir');
            $table->decimal('harga', 10, 2)->nullable();
            $table->integer('jumlah_peserta')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
