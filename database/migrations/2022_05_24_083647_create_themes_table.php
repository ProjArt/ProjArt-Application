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
            $table->string('name');
            $table->foreignId('text_color_primary_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('text_color_secondary_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('inactive_color_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('accent_color_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('primary_color_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('secondary_color_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('background_color_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('information_color_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('border_color_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');


            $table->unique([
                "id",
                "name",
                "text_color_primary_id",
                "text_color_secondary_id",
                "inactive_color_id",
                "accent_color_id",
                "primary_color_id",
                "secondary_color_id",
                "background_color_id",
                "information_color_id",
                "border_color_id",
            ], "theme_unique");
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
