<?php

namespace App\Http\Controllers;

use App\Http\Services\NotesService;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    public function fetchAll()
    {
        try {
            NotesService::fetchAllNotes(auth()->user());
        } catch (\Exception $e) {
            return httpError($e->getMessage());
        }

        return httpSuccess("Notes fetched");
    }
}
