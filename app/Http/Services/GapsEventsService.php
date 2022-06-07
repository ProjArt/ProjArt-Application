<?php

namespace App\Http\Services;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
use ICal\ICal;
use Illuminate\Support\Facades\DB;

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
        $events = $this->user->events()->nexts($items)->get();
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
            $calendar = $user->calendars()->firstOrCreate([
                'name' => "Horaires",
            ]);

            $user->calendars()->detach($calendar->id);

            $user->calendars()->attach($calendar->id, [
                'color' => random_color(),
            ]);






            try {
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
                $ical->initUrl($user->getActualHoraireLink());

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
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
        return $calendar->events; // Returns an array of events
    }
}
