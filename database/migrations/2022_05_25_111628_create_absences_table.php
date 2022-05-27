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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->string('orientation');
            $table->string('unity');
            $table->integer('e')->nullable()->default(0);
            $table->integer('t1')->nullable()->default(0);
            $table->integer('t2')->nullable()->default(0);
            $table->integer('t3')->nullable()->default(0);
            $table->integer('t4')->nullable()->default(0);
            $table->integer('total')->nullable()->default(0);
            $table->integer('relative_period')->nullable()->default(0);
            $table->integer('relative_rate')->nullable()->default(0);
            $table->integer('relative_rate_unjustified')->nullable()->default(0);
            $table->integer('absolute_period')->nullable()->default(0);
            $table->integer('absolute_rate')->nullable()->default(0);
            $table->integer('absolute_rate_unjustified')->nullable()->default(0);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('absences_rates');
    }
};
