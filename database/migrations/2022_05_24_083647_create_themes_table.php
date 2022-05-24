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
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            // $table->integer('primary_color_id');
            // $table->integer('secondary_color_id');

            $table->foreignId('primary_color_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('secondary_color_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->unique(["id", "primary_color_id", "secondary_color_id"]);
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
        Schema::dropIfExists('themes');
    }
};
