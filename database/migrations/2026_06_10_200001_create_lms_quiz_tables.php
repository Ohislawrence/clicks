<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One quiz per course (the final assessment)
        Schema::create('lms_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_course_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title')->default('Final Quiz');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('time_limit_minutes')->nullable()
                ->comment('null = no time limit');
            $table->timestamps();
        });

        // MCQ questions belonging to a quiz
        Schema::create('lms_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->json('options')->comment('Array of 2-6 answer option strings');
            $table->unsignedTinyInteger('correct_option')->comment('0-based index into options array');
            $table->text('explanation')->nullable()->comment('Shown after quiz is submitted');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // Each time a user takes the quiz
        Schema::create('lms_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_quiz_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('answers')->comment('[{question_id, selected, correct, is_correct}]');
            $table->unsignedTinyInteger('score')->comment('Percentage 0-100');
            $table->boolean('passed')->default(false);
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_quiz_attempts');
        Schema::dropIfExists('lms_quiz_questions');
        Schema::dropIfExists('lms_quizzes');
    }
};
