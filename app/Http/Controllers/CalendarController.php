<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShareCalendarRequest;
use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;
use App\Models\Calendar;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Calendrier
 *
 * APIs pour gérer les calendrier
 */
class CalendarController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Calendar::class, 'calendar');
    }

    /**
     * 
     * getCalendars
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
        $calendars = $user->calendars->map(function ($c) {
            $c->can_edit = $c->pivot->rights == Calendar::EDIT_RIGHT;
            $c->color = $c->pivot->color;
            return $c;
        });
        return httpSuccess('Calendars', $calendars);
    }

    /**
     * Store calendar
     * 
     * Crée un calendrier.
     *
     * @param  \App\Http\Requests\StoreCalendarOwnRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCalendarRequest $request)
    {
        $user = $request->user();
        $calendar = Calendar::create($request->validated());
        $user->calendars()->attach($calendar, ["rights" => Calendar::EDIT_RIGHT, "color" => random_color()]);
        return httpSuccess('Calendar added', $user->calendars, 201);
    }

    /**
     * Show calendar
     * 
     * Retourne un json contenant un calendrier.
     *
     * @param  \App\Models\CalendarOwn  $calendarOwn
     * @return \Illuminate\Http\Response
     */
    public function show(Calendar $calendar)
    {
        return httpSuccess('Calendar', $calendar);
    }

    /**
     * Update calendar
     * 
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCalendarOwnRequest  $request
     * @param  \App\Models\CalendarOwn  $calendarOwn
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCalendarRequest $request, Calendar $calendar)
    {
        $user = $request->user();
        $calendar = $user->calendars()->findOrFail($calendar->id);
        $calendar->update($request->validated());
        if ($request->has('color')) {
            $user->calendars()->updateExistingPivot($calendar->id, ['color' => $request->color]);
        }
        return httpSuccess('Calendar updated', $user->calendars);
    }

    /**
     * Delete calendar
     * 
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CalendarOwn  $calendarOwn
     * @return \Illuminate\Http\Response
     */
    public function destroy(Calendar $calendar)
    {
        $calendar->delete();
        return httpSuccess('Calendar deleted');
    }

    /**
     * Share calendar
     * 
     * Partage un calendrier avec un autre utilisateur.
     *
     * @bodyParam users une liste d'utilisateurs à qui partager le calendrier.
     * @bodyParam can_own bool Si l'utilisateur peut modifier le calendrier.
     */
    public function share(ShareCalendarRequest $request)
    {
        $calendar = Calendar::find($request->calendar_id);

        $this->authorize('share', $calendar);  // Check if user can share calendar

        foreach ($request->users as $username) {
            $user = User::whereUsername($username)->firstOrFail();
            $user->calendars()
                ->sync([$calendar->id => [
                    'rights' => $request->can_own ? Calendar::EDIT_RIGHT : Calendar::READ_RIGHT
                ]], false);
        }


        return httpSuccess('Calendar shared');
    }
}
