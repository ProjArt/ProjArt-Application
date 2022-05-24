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
        Schema::create('file_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('file_id')->on('files')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(["id", "user_id", "file_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_file');
    }
};
