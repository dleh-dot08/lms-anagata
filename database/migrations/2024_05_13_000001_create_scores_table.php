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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->foreignId('peserta_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->float('creativity_score')->nullable();
            $table->float('program_score')->nullable();
            $table->float('design_score')->nullable();
            $table->float('total_score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Untuk memastikan hanya satu nilai per peserta per pertemuan
            $table->unique(['meeting_id', 'peserta_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
}; 