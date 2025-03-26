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
        // Table Users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');  // Password yang sudah di-hash
            $table->string('foto_diri')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('tempat_lahir')->nullable();
            $table->text('alamat_tempat_tinggal')->nullable();
            $table->text('instansi')->nullable();
            $table->foreignId('jenjang_id')->nullable()->constrained('jenjang')->onDelete('set null');
            $table->string('jabatan')->nullable();
            $table->text('bidang_pengajaran')->nullable();
            $table->string('divisi')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->string('surat_tugas')->nullable();
            $table->foreignId('role_id')->constrained('roles')->onDelete('restrict');  // Relasi ke roles
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');  // User yang membuat
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');  // User yang mengupdate
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();  // Soft delete
        });

        // Table Password Reset Tokens (Bawaan Laravel)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Table Sessions (Bawaan Laravel)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
