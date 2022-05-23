<?php

namespace App\Http\Controllers;

use App\Http\Services\NotesService;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    public function fetchAll() {
        NotesService::fetchAllNotes(auth()->user());

        return httpSuccess("Notes fetched");
    }
}
