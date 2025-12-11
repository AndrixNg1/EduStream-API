<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->nullable()->index();
            $table->text('description')->nullable();
            $table->enum('type', ['video','audio','pdf','other'])->default('video');
            $table->string('original_path')->nullable(); // stored path
            $table->integer('duration')->nullable(); // seconds for audio/video
            $table->string('thumbnail')->nullable();
            $table->integer('position')->default(1);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['chapter_id','position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
