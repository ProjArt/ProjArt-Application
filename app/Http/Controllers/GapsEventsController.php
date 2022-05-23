<?php

namespace App\Http\Controllers;

use App\Http\Services\GapsEventsService;
use Illuminate\Http\Request;

class GapsEventsController extends Controller
{
    public function fetchAll()
    {
        try {
            $events = GapsEventsService::fetchAllHoraires(auth()->user());
        } catch (\Exception $e) {
            return httpError($e->getMessage());
        }

        return httpSuccess("Horaires fetched", $events);
    }
}
