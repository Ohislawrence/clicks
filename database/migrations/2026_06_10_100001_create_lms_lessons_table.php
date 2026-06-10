<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable(); // Rich text / markdown
            $table->string('video_url')->nullable(); // YouTube / Vimeo embed URL
            $table->integer('duration_minutes')->default(0);
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_free_preview')->default(false); // visible without enrollment
            $table->timestamps();

            $table->unique(['lms_course_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_lessons');
    }
};
