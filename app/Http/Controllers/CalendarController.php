<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;
use App\Models\Calendar;
use Illuminate\Http\Request;

/**
 * @group Calendrier
 *
 * APIs pour gérer les calendrier
 */
class CalendarController extends Controller
{
    /**
     * 
     * Obtenir tous les calendrier
     * 
     * Retourne un json contenant une liste des calendrier. La liste correspond à l'ensemble des évènements de tous les calendriers que suit l'utilisateur.
     *
     * 
     * @response scenario=success [
     *  "id" => 1,
     *  "name" => "Calendrier 1",
     *  "events" => [
     *      "id" => 1,
     *      "title" => "Event 1",
     *      "start" => "2020-01-01",
     *      "end" => "2020-01-01",
     *      "description" => "Description 1",
     *      "location" => "Location 1",
     *      "calendar_id" => 1,
     *      "created_at" => "2020-01-01",
     *      "updated_at" => "2020-01-01",
     *   ]    
     * ]
     *  
     *    
     * @authenticated
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return httpSuccess('Calendars', $user->calendarsOwn);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCalendarRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCalendarRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function show(Calendar $calendar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCalendarRequest  $request
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCalendarRequest $request, Calendar $calendar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Calendar $calendar)
    {
        //
    }
}
