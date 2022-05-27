<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportICSRequest;
use App\Http\Services\GapsEventsService;
use App\Models\Calendar;
use App\Models\CalendarOwn;
use ICal\ICal;
use Illuminate\Http\Request;


/**
 * @group Gaps
 *
 * APIs pour forcer la mis à jour de toutes les données de Gaps
 */
class GapsEventsController extends Controller
{

    public function fetchAll()
    {
        try {
            $events = GapsEventsService::fetchAllHoraires(auth()->user());
        } catch (\Exception $e) {
            return httpError($e->getMessage());
        }

        return httpSuccess("Horaires fetched", $events);
    }


    /*
     * Import calendar ICS
     * 
     * Importe un calendrier ICS.
     * 
     * @bodyParam name string required Calendar name.
     * @bodyParam ics file required ICS file.
     * 
     */
    public function importCalendarICS(ImportICSRequest $request)
    {
        $user = $request->user();
        $ical = new ICal('ICal.ics', array(
            'defaultSpan'                 => 2,     // Default value
            'defaultTimeZone'             => 'UTC',
            'defaultWeekStart'            => 'MO',  // Default value
            'disableCharacterReplacement' => false, // Default value
            'filterDaysAfter'             => null,  // Default value
            'filterDaysBefore'            => null,  // Default value
            'skipRecurrence'              => false, // Default value
        ));
        $ical->initFile($request->file('ics'));

        $calendar = CalendarOwn::create(
            [
                'name' => $request->name,
            ]
        );

        $user->calendarsOwn()->save($calendar);

        foreach ($ical->events() as $event) {
            $calendar->events()->create([
                'calendar_id' => $calendar->id,
                'title' => $event->summary,
                'description' => $event->description,
                'start' => $event->dtstart,
                'end' => $event->dtend,
                'location' => $event->location ?? "",
            ]);
        }

        return httpSuccess('Calendar added', $calendar->events, 201);
    }
}
