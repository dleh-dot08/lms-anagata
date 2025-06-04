<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_mentors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['primary', 'backup'])->default('backup');
            $table->integer('priority')->default(1); // For ordering backup mentors (1, 2, 3)
            $table->text('notes')->nullable(); // Optional notes about mentor's role
            $table->timestamps();
            
            // Ensure a mentor can't have multiple roles in the same course
            $table->unique(['course_id', 'mentor_id']);
        });

        // Move existing mentor relationships to the new table
        Schema::table('courses', function (Blueprint $table) {
            // Mark the old mentor_id as nullable before we migrate the data
            $table->foreignId('mentor_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revert courses table changes
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('mentor_id')->nullable(false)->change();
        });

        Schema::dropIfExists('course_mentors');
    }
};