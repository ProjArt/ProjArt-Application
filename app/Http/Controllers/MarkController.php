<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarkRequest;
use App\Http\Requests\UpdateMarkRequest;
use App\Http\Services\GapsMarksService;
use App\Models\Mark;
use Illuminate\Http\Request;

/**
 * @group Notes
 *
 * APIs pour gérer les notes
 */
class MarkController extends Controller
{
    /**
     * 
     * Get marks
     * 
     * Retourne un json contenant une liste des notes de l'utilisateur.
     *
     * 
     * @response scenario=success 
     * 
     * {
     * "2020 - 2021": [
     *   {
     *     "id": 1,
     *     "module_code": "MODULE",
     *     "module_name": "TEXT",
     *     "value": X,
     *     "year_start": X,
     *     "year_end": X
     *   },
     * }
     *    
     * @authenticated
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        /* $marks =  $user->marks->groupBy(function ($mark) {
            return $mark->year_start . " - " . $mark->year_end;
        }); */
        return httpSuccess('Les notes', $user->markmodules);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMarkRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMarkRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function show(Mark $mark)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMarkRequest  $request
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMarkRequest $request, Mark $mark)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mark $mark)
    {
        //
    }
}
