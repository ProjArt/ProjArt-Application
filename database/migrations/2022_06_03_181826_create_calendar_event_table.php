<?php

use App\Models\Calendar;
use App\Models\Event;
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
        Schema::create('calendar_event', function (Blueprint $table) {
            $table->foreignIdFor(Calendar::class)->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(Event::class)->constrained()->onDelete('cascade')->onUpdate('cascade');

            $table->primary(['calendar_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_event');
    }
};
