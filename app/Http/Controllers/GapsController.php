<?php

namespace App\Http\Controllers;

use App\Http\Services\GapsAbsencesService;
use App\Http\Services\GapsEventsService;
use App\Http\Services\GapsMarksService;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Gaps
 */
class GapsController extends Controller
{
    /**
     * 
     * Update all Gaps
     * 
     * Pour forcer la mise à jour des données provenant de Gaps
     *
     * 
     * @authenticated
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request)
    {
        $user = $request->user();
        GapsEventsService::fetchAllHoraires($user);
        if ($user->role == User::ROLE_STUDENT) {
            GapsMarksService::fetchAllNotes($user);
            GapsAbsencesService::fetchAllAbsences($user);
        }
        return httpSuccess('All fetched', ["user" => $user]);
    }

    /**
     * 
     * Update all Gaps Cron
     * 
     * Pour forcer la mise à jour des données provenant de Gaps depuis un cron
     *
     * 
     * @authenticated
     * @return \Illuminate\Http\Response
     */
    public function updateAllCron(Request $request)
    {
        return $this->updateAll($request);
    }
}
