<?php

namespace App\Http\Controllers;

use App\Http\Services\GapsEventsService;
use App\Http\Services\GapsMarksService;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Gaps
 */
class GapsController extends Controller
{
    /*
     *
     * Pour forcer la mise à jour de toutes les données de Gaps
     *
     * Ne retourne rien
     * 
    */
    public function updateAll(Request $request)
    {
        $user = $request->user();
        GapsEventsService::fetchAllHoraires($user);
        GapsMarksService::fetchAllNotes($user);
        return httpSuccess('All fetched', ["user" => $user]);
    }
}
