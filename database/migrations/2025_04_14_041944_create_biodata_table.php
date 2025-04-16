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
        Schema::create('biodata', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->unsignedBigInteger('id_user'); // Foreign key to User
            $table->string('nip')->nullable();
            $table->string('nama_lengkap');
            $table->string('jabatan')->nullable();
            $table->string('divisi')->nullable();
            $table->string('nik')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('foto')->nullable();
            $table->string('data_ktp')->nullable();
            $table->string('data_ttd')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biodata');
    }
};
