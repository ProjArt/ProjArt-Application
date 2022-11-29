<?php

namespace App\Http\Services;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;

class GapsAbsencesService
{
    public function __construct(private User $user)
    {
    }

    public function fetchAbsences()
    {
        $absences = $this->user->absences;
        return $this->displayAbsences($absences);
    }

    public function fetchFuturesHoraires($items = 1)
    {
        $absences = $this->user->absences;
        return $this->displayAbsences($absences);
    }

    private function displayAbsences($absences)
    {
        $s = "";

        /*  foreach ($events as $event) {
            $start = \Carbon\Carbon::parse($event->start);
            $end = \Carbon\Carbon::parse($event->end);

            $isNow = $this->isPast($event->start) && !$this->isPast($event->end);


            $s .= ($isNow ? "<i>" : "") . "<strong>Titre : </strong>"  . $event->title . ($isNow ? "</i>" : "") . "\n";
            $s .= ($isNow ? "<i>" : "") . "<strong>Début : </strong>"  . $start->translatedFormat('d F à H\hi') . ($isNow ? "</i>" : "") . "\n";
            $s .= ($isNow ? "<i>" : "") . "<strong>Fin : </strong>"  . $end->translatedFormat('d F à H\hi') . ($isNow ? "</i>" : "") . "\n";
            $s .= ($isNow ? "<i>" : "") . "<strong>Lieu : </strong>" . $event->location .  ($isNow ? "</i>" : "") . "\n";
            $s .= "\n";
        } */

        return $s;
    }

    private function isPast($date)
    {
        return strtotime($date) < strtotime('now');
    }

    public static function fetchAllAbsences($user = null)
    {
        $users = $user != null ? [$user] : User::all();

        foreach ($users as $user) {

            if ($user->role != User::ROLE_STUDENT) {
                continue;
            }

            $args =  '["studentAbsGrid_rateSelectorId", "studentAbsGrid", null, null, null, "2022", "0", "' . $user->gaps_id . '", null]';

            try {
                $user->setPersonalNumber();
                $response = Http::withBasicAuth($user->username, $user->password)->asForm()->post('https://gaps.heig-vd.ch/consultation/etudiant/', [
                    "rs" => "smartReplacePart",
                    "rsargs" => $args
                ]);
                $res =  str_replace('\u00a3', '', $response->body());
                $res =  str_replace('@', '', $res);
                $res =  str_replace('+:"', '', $res);
                $res =  str_replace('\n', '', $res);
                $res =  str_replace('\\', '', $res);
                $res =  str_replace('&nbsp', '', $res);
                $res =  str_replace('u00e9', 'é', $res);
                $res =  str_replace('u00e0', 'à', $res);
                $res =  str_replace('u00e7', 'ç', $res);
                $res =  str_replace('u00e2', 'â', $res);
                $res =  str_replace('u00e4', 'ä', $res);
                $res =  str_replace('u00f6', 'ö', $res);
                $res =  str_replace('u00fc', 'ü', $res);
                $res =  str_replace('u00f1', 'ñ', $res);
                $res =  str_replace('u00e8', 'è', $res);
                $res =  str_replace('u00f4', 'ô', $res);
                $res =  str_replace('u00fb', 'û', $res);
                $res =  str_replace('%', '', $res);

                $res = substr_replace($res, "", -1);

                $dom = HtmlDomParser::str_get_html($res);

                $tds = $dom->find("tr.a_r_0")[0]->findMulti("td");

                $orientation = $tds["1"]->innerText;
                $unity = $tds["2"]->innerText;
                $e = (int)$tds["3"]->innerText;
                $t1 = (int)$tds["4"]->innerText;
                $t2 = (int)$tds["5"]->innerText;
                $t3 = (int)$tds["6"]->innerText;
                $t4 = (int)$tds["7"]->innerText;
                $total = (int)$tds["8"]->innerText;
                $relative_period = (int)$tds["9"]->innerText;
                $relative_rate = (int)$tds["10"]->innerText;
                $relative_rate_unjustified = (int)$tds["11"]->innerText;
                $absolute_period = (int)$tds["12"]->innerText;
                $absolute_rate = (int)$tds["13"]->innerText;
                $absolute_rate_unjustified = (int)$tds["14"]->innerText;

                $absence = compact('orientation', 'unity', 'e', 't1', 't2', 't3', 't4', 'total', 'relative_period', 'relative_rate', 'relative_rate_unjustified', 'absolute_period', 'absolute_rate', 'absolute_rate_unjustified');

                $user->absences()->updateOrCreate(
                    $absence
                );
                return $user->absences;
            } catch (\Exception $exception) {
                return $exception->getMessage();
            }
        }
        return []; // Returns an array of events
    }
}
