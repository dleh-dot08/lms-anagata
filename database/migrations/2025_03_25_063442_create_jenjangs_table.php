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
        Schema::create('jenjang', function (Blueprint $table) {
            $table->id();  // Primary Key
            $table->string('nama_jenjang', 50)->unique();  // Nama jenjang unik, max 50 karakter
            $table->text('description')->nullable();  // Deskripsi opsional
            $table->timestamps();  // created_at & updated_at otomatis
            $table->softDeletes();  // Untuk soft delete (deleted_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenjang');
    }
};
