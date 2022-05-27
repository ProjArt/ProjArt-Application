<?php

namespace App\Http\Controllers;

use App\Http\Services\GapsAbsencesService;
use Illuminate\Http\Request;

class GapsAbsenceController extends Controller
{
    public function fetchAll()
    {
        try {
            $results = GapsAbsencesService::fetchAllAbsences(auth()->user());
        } catch (\Exception $e) {
            return httpError($e->getMessage());
        }

        return $results; //httpSuccess("Horaires fetched", $result);
    }
}
