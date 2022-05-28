<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShareCalendarRequest;
use App\Http\Requests\StoreCalendarOwnRequest;
use App\Http\Requests\UpdateCalendarOwnRequest;
use App\Models\Calendar;
use App\Models\CalendarOwn;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Calendrier
 *
 * APIs pour gérer les calendrier
 */
class CalendarOwnController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(CalendarOwn::class, 'calendar');
    }

    /**
     * 
     * Get calendars
     * 
     * Retourne un json contenant une liste des calendrier. La liste correspond à l'ensemble des évènements de tous les calendriers que suit l'utilisateur.
     *
     * 
     * @response scenario=success [
     *  {
     *    "id": 1,
     *    "name": "NAME"
     *  }
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
     * Store calendar
     * 
     * Crée un calendrier.
     *
     * @param  \App\Http\Requests\StoreCalendarOwnRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCalendarOwnRequest $request)
    {
        $user = $request->user();
        $calendar = Calendar::create($request->validated());
        $user->calendarsOwn()->attach($calendar);
        return httpSuccess('Calendar added', $user->calendarsOwn, 201);
    }

    /**
     * Show calendar
     * 
     * Retourne un json contenant un calendrier.
     *
     * @param  \App\Models\CalendarOwn  $calendarOwn
     * @return \Illuminate\Http\Response
     */
    public function show(CalendarOwn $calendar)
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
    public function update(UpdateCalendarOwnRequest $request, CalendarOwn $calendar)
    {
        $user = $request->user();
        $calendar->update($request->validated());
        return httpSuccess('Calendar updated', $user->calendarsOwn);
    }

    /**
     * Delete calendar
     * 
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CalendarOwn  $calendarOwn
     * @return \Illuminate\Http\Response
     */
    public function destroy(CalendarOwn $calendar)
    {
        $calendar->delete();
        return httpSuccess('Calendar deleted');
    }

    /**
     * Share calendar
     * 
     * Partage un calendrier avec un autre utilisateur.
     *
     * @bodyParam user_id int required ID de l'utilisateur à qui partager le calendrier.
     * @bodyParam calendar_id int required ID du calendrier à partager.           
     */
    public function share(ShareCalendarRequest $request)
    {
        $userToShare = User::find($request->user_id);
        $calendar = CalendarOwn::find($request->calendar_id);

        $this->authorize('share', $calendar);  // Check if user can share calendar

        if ($request->can_view) {
            $userToShare->calendarsFollow()->sync($calendar);
        }
        if ($request->can_own) {
            $userToShare->calendarsOwn()->sync($calendar);
        }
        return httpSuccess('Calendar shared');
    }
}
