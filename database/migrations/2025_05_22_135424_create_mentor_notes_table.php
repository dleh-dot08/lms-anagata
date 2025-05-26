<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mentor_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->text('materi')->nullable();
            $table->text('project')->nullable();
            $table->text('sikap_siswa')->nullable();
            $table->text('hambatan')->nullable();
            $table->text('solusi')->nullable();
            $table->text('masukan')->nullable();
            $table->text('lain_lain')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_notes');
    }
};

