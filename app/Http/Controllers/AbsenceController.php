<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $absences = $user->absences;
        return httpSuccess("Absences fetched", $user->absences);
    }
}
