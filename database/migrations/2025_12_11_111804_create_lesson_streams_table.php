<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lesson_streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('quality')->nullable(); // e.g. 360p, 720p, pdf-preview
            $table->string('file_path'); // relative path in storage
            $table->enum('container', ['mp4','m3u8','mp3','pdf','other'])->default('mp4');
            $table->string('mime')->nullable();
            $table->integer('filesize')->nullable();
            $table->timestamps();

            $table->index(['lesson_id','quality']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_streams');
    }
};
