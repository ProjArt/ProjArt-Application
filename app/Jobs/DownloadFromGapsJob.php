<?php

namespace App\Jobs;

use App\Http\Services\GapsAbsencesService;
use App\Http\Services\GapsEventsService;
use App\Http\Services\GapsMarksService;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\OneSignal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DownloadFromGapsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private User $user)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        GapsEventsService::fetchAllHoraires($this->user);
        GapsMarksService::fetchAllNotes($this->user);
        GapsAbsencesService::fetchAllAbsences($this->user);

        (new Notification())->send();
    }
}
