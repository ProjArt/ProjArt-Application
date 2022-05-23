<?php

namespace App\Http\Controllers;

use App\Http\Services\HorairesService;
use Illuminate\Http\Request;

class HorairesController extends Controller
{
    public function fetchAll() {
        HorairesService::fetchAllHoraires(auth()->user());

        return httpSuccess("Horaires fetched");
    }
}
