<?php

namespace App\Http\Controllers;

use App\Http\Services\HorairesService;
use Illuminate\Http\Request;

class HorairesController extends Controller
{
    public function fetchAll()
    {
        try {
            HorairesService::fetchAllHoraires(auth()->user());
        } catch (\Exception $e) {
            return httpError($e->getMessage());
        }

        return httpSuccess("Horaires fetched");
    }
}
