<?php

namespace App\Http\Controllers;

use App\Http\Services\GapsEventsService;
use App\Http\Services\GapsMarksService;
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
    public function updateAll()
    {
        GapsEventsService::fetchAllHoraires();
        GapsMarksService::fetchAllNotes();
        return httpSuccess('All fetched');
    }
}
