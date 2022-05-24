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
        Schema::create('telegraph_chat_user', function (Blueprint $table) {
            $table->foreignId("user_id")->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('telegraph_chat_id')->constrained('telegraph_chats')->onDelete('cascade')->onUpdate('cascade');
            $table->primary(['user_id', 'telegraph_chat_id']);

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
        Schema::dropIfExists('telegraph_chat_user');
    }
};
