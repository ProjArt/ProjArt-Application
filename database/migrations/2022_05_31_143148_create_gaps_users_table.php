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
        Schema::create('gaps_users', function (Blueprint $table) {
            $table->string("username")->primary();
            $table->string("firstname");
            $table->string("name");
            $table->string("mail")->nullable();
            $table->string("gaps_username")->nullable();
            $table->boolean("is_teacher");
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
        Schema::dropIfExists('gaps_users');
    }
};
