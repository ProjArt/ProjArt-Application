<?php

namespace App\Http\Services;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class GapsEventsService
{
    public function __construct(private User $user)
    {
    }

    public function fetchHoraires()
    {
        $events = $this->user->horaire;
        return $this->displayHoraires($events);
    }

    public function fetchFuturesHoraires($items = 1)
    {
        $events = [];
        $calendars = $this->user->calendars;

        foreach ($calendars as $calendar) {
            foreach ($calendar->events as $event) {
                $events[] = $event;
            }
        }

        return count($events);


        return $this->displayHoraires($events);
    }

    private function displayHoraires($events)
    {
        $s = "";

        foreach ($events as $event) {
            $start = \Carbon\Carbon::parse($event->start);
            $end = \Carbon\Carbon::parse($event->end);

            $isNow = $this->isPast($event->start) && !$this->isPast($event->end);


            $s .= ($isNow ? "<i>" : "") . "<strong>Titre : </strong>"  . $event->title . ($isNow ? "</i>" : "") . "\n";
            $s .= ($isNow ? "<i>" : "") . "<strong>Début : </strong>"  . $start->translatedFormat('d F à H\hi') . ($isNow ? "</i>" : "") . "\n";
            $s .= ($isNow ? "<i>" : "") . "<strong>Fin : </strong>"  . $end->translatedFormat('d F à H\hi') . ($isNow ? "</i>" : "") . "\n";
            $s .= ($isNow ? "<i>" : "") . "<strong>Lieu : </strong>" . $event->location .  ($isNow ? "</i>" : "") . "\n";
            $s .= "\n";
        }

        return $s;
    }

    private function isPast($date)
    {
        return strtotime($date) < strtotime('now');
    }

    public static function fetchAllHoraires($user = null)
    {

        $users = $user != null ? [$user] : User::all();

        foreach ($users as $user) {
            if (!$user->calendars()->whereName('Horaires')->exists()) {
                $calendar = Calendar::create([
                    'name' => 'Horaires',
                ]);
                $user->calendars()->attach($calendar->id, [
                    'color' => random_color(),
                    'rights' => Calendar::EDIT_RIGHT
                ]);
            } else {
                $calendar = $user->calendars()->whereName('Horaires')->first();
            }


            $user->setPersonalNumber();
            $ical = new ICal('ICal.ics', array(
                'defaultSpan'                 => 2,     // Default value
                'defaultTimeZone'             => 'UTC',
                'defaultWeekStart'            => 'MO',  // Default value
                'disableCharacterReplacement' => false, // Default value
                'filterDaysAfter'             => null,  // Default value
                'filterDaysBefore'            => null,  // Default value
                'skipRecurrence'              => false, // Default value
            ));

            //https://gaps.heig-vd.ch/consultation/horaires/?annee=2021&trimestre=4&type=2&id=17903&icalendarversion=2&individual=1
            //https://charles.matrand:3%40UUs%40%404x95vnMB@gaps.heig-vd.ch/consultation/horaires/?annee=2021&trimestre=4&type=2&id=17903&icalendarversion=2&individual=1

            $response = Http::withBasicAuth($user->username, $user->password)->get($user->getActualHoraireLink());

            /* $ical = new iCal_Parser();
            $ical->parse($response->body());


            return $ical->get_events();
 */

            $ical->initString($response->body());

            $calendar->events()->delete();

            foreach ($ical->events() as $event) {
                $event = Event::firstOrCreate([
                    'title' => $event->summary,
                    'description' => $event->description,
                    'start' => $event->dtstart,
                    'end' => $event->dtend,
                    'location' => $event->location ?? "",
                ]);

                $calendar->events()->attach($event);
            }
        }
        return $calendar->events; // Returns an array of events
    }
}
