<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Calendar;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller
{
    /**
     * Retourne un json contenant une liste des évènements. La liste correspond à l'ensemble des évènements de tous les calendriers que suit l'utilisateur.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $calendars = $request->user()->calendarsFollow->map(function ($calendar) {
            return [
                "id" => $calendar->id,
                "name" => $calendar->name,
                "events" => $calendar->events()->orderBy('start')->get()
            ];
        });

        return httpSuccess('user', $calendars);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEventRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return httpSuccess('Events', $event);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEventRequest  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }



    //Renvoit un objet json contenant une liste d'évènements. Elle correspond à l'ensemble des évènements de la classe
    public function getCalendarEvents($calendarId)
    {
        $calendar = Calendar::findOrFail($calendarId);
        return httpSuccess('Events', $calendar->events);
    }
}
