<?php

namespace App\Http\Services;

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

        if ($user == null) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            //DB::table('horaires')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $users = $user != null ? [$user] : User::all();

        foreach ($users as $user) {
            $args =  '["studentAbsGrid_rateSelectorId", "studentAbsGrid", null, null, null, "2021", "0", "' . $user->gaps_id . '", null]';

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

                $res = substr_replace($res, "", -1);

                $dom = HtmlDomParser::str_get_html($res);

                $tds = $dom->find("tr.a_r_0")[0]->findMulti("td");

                $etudiant = $tds["0"]->innerText;
                $orientation = $tds["1"]->innerText;
                $unite = $tds["2"]->innerText;
                $e = $tds["3"]->innerText;
                $t1 = $tds["4"]->innerText;
                $t2 = $tds["5"]->innerText;
                $t3 = $tds["6"]->innerText;
                $t4 = $tds["7"]->innerText;
                $total = $tds["8"]->innerText;
                $periodeRelatif = $tds["9"]->innerText;
                $tauxRelatif = $tds["10"]->innerText;
                $tauxHorsJustifies = $tds["11"]->innerText;
                $periodeAbsolu = $tds["12"]->innerText;
                $tauxAbsolu = $tds["13"]->innerText;
                $tauxHorsJustifiesAbsolu = $tds["14"]->innerText;

                $absence = compact('etudiant', 'orientation', 'unite', 'e', 't1', 't2', 't3', 't4', 'total', 'periodeRelatif', 'tauxRelatif', 'tauxHorsJustifies', 'periodeAbsolu', 'tauxAbsolu', 'tauxHorsJustifiesAbsolu');

                return $absence;
            } catch (\Exception $exception) {
                return $exception->getMessage();
            }
        }
        return []; // Returns an array of events
    }
}
