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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // foreign key ke users
            $table->unsignedBigInteger('course_id')->nullable(); // foreign key ke courses, nullable
            $table->string('title', 255);
            $table->longText('html_code')->nullable();
            $table->longText('css_code')->nullable();
            $table->longText('js_code')->nullable();
            $table->timestamps(); // created_at dan updated_at
            $table->timestamp('deleted_at')->nullable(); // soft deletes
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
