<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Calendar;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group Evènements
 *
 * APIs pour gérer les évènements
 */
class EventController extends Controller
{
    /**
     * 
     * Obtenir tous les évènements
     * 
     * Retourne un json contenant une liste des évènements. La liste correspond à l'ensemble des évènements de tous les calendriers que suit l'utilisateur.
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
     * 
     * @hideFromAPIDocumentation
     *
     * @param  \App\Http\Requests\StoreEventRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventRequest $request)
    {
        //
    }

    /**
     * @hideFromAPIDocumentation
     * 
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
     * @hideFromAPIDocumentation
     * 
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
     * 
     * @hideFromAPIDocumentation
     * 
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }


    /* 
     * 
     * Obtenir les évènements d'un calendrier
     *   
     * Renvoit un objet json contenant une liste d'évènements. Elle correspond à l'ensemble des évènements de la classe
     
    public function getCalendarEvents($calendarId)
    {
        $calendar = Calendar::findOrFail($calendarId);
        return httpSuccess('Events', $calendar->events);
    } */
}
