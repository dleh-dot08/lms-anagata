<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();  // Auto Increment ID
            $table->string('name', 50)->unique();  // Nama Role harus unik
            $table->text('description')->nullable();  // Deskripsi Role
            $table->timestamps();  // created_at dan updated_at otomatis
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

