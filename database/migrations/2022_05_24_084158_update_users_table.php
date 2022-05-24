<?php

use App\Models\User;
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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('theme_id')->default(1)->constrained('themes')->onDelete('restrict')->onUpdate('restrict');
            $table->integer('gaps_id')->default(0);
            $table->enum('role', User::ROLES)->default(User::ROLE_STUDENT);
            $table->unique(['username'], 'user_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
