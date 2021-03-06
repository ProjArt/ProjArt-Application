<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('course_code', 50);
            $table->foreign('course_code')->references('code')->on('courses')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('course_teacher_id')->constrained('teachers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('course_teacher_user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('event_calendar_id')->constrained('calendars')->onUpdate('cascade')->onDelete('cascade');
            $table->string('room');

            $table->unique(['course_code', 'course_teacher_id', 'course_teacher_user_id', 'event_id', 'event_calendar_id', 'id'], 'primary_keys_lessons');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lessons');
    }
};
