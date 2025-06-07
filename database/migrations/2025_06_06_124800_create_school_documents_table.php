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
        Schema::create('school_documents', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            
            $table->foreignId('sekolah_id')
                  ->constrained('sekolah') // Menghubungkan ke tabel 'schools'
                  ->onDelete('cascade');  // Jika sekolah dihapus, dokumen juga dihapus

            $table->string('document_type', 50); // Contoh: 'ktp_kepsek', 'npwp', 'logo'
            $table->string('file_path');    // Path file di storage
            $table->string('file_name');    // Nama asli file
            $table->string('mime_type', 100); // Tipe file (e.g., 'application/pdf')
            $table->unsignedInteger('file_size'); // Ukuran file dalam bytes

            $table->foreignId('uploaded_by_user_id')
                  ->nullable()           // Bisa null jika tidak ada user yang mengunggah
                  ->constrained('users') // Menghubungkan ke tabel 'users'
                  ->onDelete('set null'); // Jika user dihapus, kolom ini menjadi null

            $table->timestamps(); // created_at dan updated_at

            // Memastikan hanya ada satu file per jenis dokumen per sekolah
            $table->unique(['sekolah_id', 'document_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_documents');
    }
};