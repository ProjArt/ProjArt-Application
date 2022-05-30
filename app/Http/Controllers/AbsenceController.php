<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


/**
 * @group Absences
 *
 * APIs pour gérer les absences
 */
class AbsenceController extends Controller
{

    /**
     * 
     * Get absences
     * 
     * Retourne un json contenant une liste des absences.
     *
     * 
     * @authenticated
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $absences = $user->absences;
        return httpSuccess("Absences fetched", $user->absences);
    }
}
