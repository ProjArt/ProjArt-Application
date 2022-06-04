<?php

namespace App\Http\Services;

use App\Models\Mark;
use App\Models\MarkModule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;



class GapsMarksService
{
    public function __construct(private User $user)
    {
    }

    public function fetchNotes()
    {
        $notes = $this->user->marks()->where('year_end', "=", now()->year)->orWhere('year_start', "=", now()->year)->orderBy('module_code')->get();
        return $this->displayNotes($notes);
    }

    private function displayNotes($notes)
    {
        $s = "";
        foreach ($notes as $note) {
            $s .= "<strong>{$note->module_code}</strong> : {$note->value} \n";
            //$s .= "<strong>" . $note->module_code . ": </strong>" . strval($note->note) . "\n";
        }
        return $s;
    }

    public static function fetchAllNotes($user = null)
    {

        $users = $user != null ? [$user] : User::all();

        foreach ($users as $user) {
            try {
                $user->setPersonalNumber();

                $response = Http::withBasicAuth($user->username, $user->password)->get("https://@gaps.heig-vd.ch/consultation/notes/bulletin.php?id=" . $user->gaps_id);

                $dom = HtmlDomParser::str_get_html($response->body());

                $trs = $dom->findMulti("#record_table tr");

                $marks = [];
                $module = "";

                foreach ($trs as $tr) {

                    if ($tr->class == "bulletin_module_row") {
                        $tds = $tr->findMulti("td");
                        $moduleCode = $tds[0]->innerText;
                        $moduleName = $tds[1]->innerText;
                        $moduleStatus = $tds[2]->innerText;
                        $moduleYears = $tds[3]->innerText;
                        $moduleMark = $tds[4]->innerText;
                        $moduleCredits = $tds[6]->innerText;
                        $module = $user->markmodules()->firstOrCreate([
                            'code' => $moduleCode,
                            'name' => $moduleName,
                            'status' => $moduleStatus,
                            'years' => $moduleYears,
                            'credits' => $moduleCredits,
                            'mark' => $moduleMark
                        ]);
                    }
                    if ($tr->class == "bulletin_unit_row") {
                        $tds = $tr->findMulti("td");
                        $courseCode = $tds[0]->innerText;
                        $courseName = $tds[1]->innerText;
                        $note = $tds[4]->innerText;
                        $yearStart = explode("-", $tds[3]->innerText)[0];
                        $yearEnd = explode("-", $tds[3]->innerText)[1];

                        $mark = $user->marks()->firstOrCreate([
                            'markmodule_id' => $module->id,
                            'course_code' => $courseCode,
                            'course_name' => $courseName,
                            'value' => explode(" ", str_replace("<br>", " ", $note))[0],
                            'year_start' => $yearStart,
                            'year_end' => $yearEnd,
                        ]);

                        $marks[] = $mark;
                    }
                }
            } catch (\Throwable $th) {
                // echo $th->getMessage();
                return $th->getMessage();
            }
        }
        return "ok";
    }
}
