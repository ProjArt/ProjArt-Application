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
        Schema::create('course_gaps_user', function (Blueprint $table) {
            $table->id();
            $table->string("gaps_user_username");
            $table->string("course_code");
            $table->foreign('gaps_user_username')->references("username")->on("gaps_users")->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('course_code')->references("code")->on("courses")->onDelete('cascade')->onUpdate('cascade');
            $table->unique(["id", "gaps_user_username", "course_code"]);
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
        Schema::dropIfExists('course_gaps_user');
    }
};
