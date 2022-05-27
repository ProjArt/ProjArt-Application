<?php

namespace App\Http\Controllers;

use App\Http\Services\GapsMarksService;
use Illuminate\Http\Request;

/**
 * @group Gaps
 *
 * APIs pour forcer la mis à jour de toutes les données de Gaps
 */
class GapsMarksController extends Controller
{
    public function fetchAll()
    {
        try {
            $marks = GapsMarksService::fetchAllNotes(auth()->user());
        } catch (\Exception $e) {
            return httpError($e->getMessage());
        }

        return httpSuccess("Notes fetched", $marks);
    }
}
