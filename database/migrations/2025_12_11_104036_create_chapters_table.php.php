<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();

            $table->integer('position')->default(1);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_published')->default(false);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['course_id', 'slug'], 'chapters_course_slug_unique');
            $table->index(['course_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
