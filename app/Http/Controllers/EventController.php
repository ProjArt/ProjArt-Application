<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Calendar;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;

/**
 * @group Evènements
 *
 * APIs pour gérer les évènements
 */
class EventController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Event::class, 'event');
        //$this->middleware('canManageEtablissement', ['only' => ['store', 'update', 'destroy']]);
    }
    /**
     * 
     * Get events
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
        //$events = $user->events()->with('calendar')->get()->groupBy('calendar_id');
        $calendars = $request->user()->calendars->map(function ($calendar) {
            return [
                "id" => $calendar->id,
                "name" => $calendar->name,
                "can_edit" => $calendar->pivot->rights == Calendar::EDIT_RIGHT,
                "color" => $calendar->pivot->color,
                "events" => $calendar->events->map(function ($event) use ($calendar) {
                    $event['color'] = $calendar->pivot->color;
                    return $event;
                })
            ];
        });

        return httpSuccess('user', $calendars);
    }
    /**
     * 
     * 
     * Store event
     * 
     * Enregistre un nouvel évènement dans la BDD
     * Succès: retourne un json contenant l'évènement créé
     * Fail: erreur
     * 
     * @response scenario=success [
     *"success" => true, 
     *"message" => "event created:", 
     *"data" => [
     *     "title" => "EventTitle", 
     *      "description" => "Une belle description", 
     *      "start" => "1901-01-01T00:00Z", 
     *      "end" => "1901-01-03T00:00Z", 
     *      "location" => "S149", 
     *      "calendar_id" => 1, 
     *      "updated_at" => "2022-05-25T13:09:40.000000Z", 
     *      "created_at" => "2022-05-25T13:09:40.000000Z", 
     *      "id" => 142 
     *   ] 
     *] 
     *  
     *
     * @param  \App\Http\Requests\StoreEventRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventRequest $request)
    {
        if ($request->start > $request->end) {
            return httpError('start date must be before end date');
        }
        $user = $request->user();
        $inputs = $request->all() + ['user_id' => $user->id];
        $calendar = Calendar::findOrFail($request->calendar_id);

        $this->authorize('store', [Event::class, $calendar]);

        $newEvent = $calendar->events()->create($inputs);

        return httpSuccess('event created:', $newEvent);
    }

    /**
     * Show event
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
     * Update event
     * 
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEventRequest  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        if ($request->start > $request->end) {
            return httpError('start date must be before end date');
        }
        $event->update($request->validated());
        return httpSuccess('Events', $event);
    }

    /**
     * 
     * Delete event
     * 
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return httpSuccess('Event deleted');
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
