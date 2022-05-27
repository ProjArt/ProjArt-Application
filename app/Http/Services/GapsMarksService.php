<?php

namespace App\Http\Services;

use App\Models\Mark;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use voku\helper\HtmlDomParser;



class GapsMarksService
{
    public function __construct(private User $user)
    {
    }

    public function fetchNotes()
    {
        $notes = $this->user->notes()->where('yearEnd', "=", now()->year)->orWhere('yearStart', "=", now()->year)->orderBy('module_code')->get();
        return $this->displayNotes($notes);
    }

    private function displayNotes($notes)
    {
        $s = "";
        foreach ($notes as $note) {
            $s .= "<strong>{$note->module_code}</strong> : {$note->note} \n";
            //$s .= "<strong>" . $note->module_code . ": </strong>" . strval($note->note) . "\n";
        }
        return $s;
    }

    public static function fetchAllNotes($user = null)
    {
        if ($user == null) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            //DB::table('marks')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $users = $user != null ? [$user] : User::all();

        foreach ($users as $user) {
            try {
                $user->setPersonalNumber();

                $content = file_get_contents("https://" . $user->username . ":" . urlencode($user->password) . "@gaps.heig-vd.ch/consultation/notes/bulletin.php?id=" . $user->gaps_id);

                $dom = HtmlDomParser::str_get_html($content);

                $trs = $dom->findMulti("#record_table tr");

                $marks = [];
                foreach ($trs as $tr) {
                    if ($tr->class == "bulletin_unit_row") {
                        $tds = $tr->findMulti("td");
                        $moduleCode = $tds[0]->innerText;
                        $moduleName = $tds[1]->innerText;
                        $note = $tds[4]->innerText;
                        $yearStart = explode("-", $tds[3]->innerText)[0];
                        $yearEnd = explode("-", $tds[3]->innerText)[1];

                        $mark = $user->marks()->firstOrCreate([
                            'module_code' => $moduleCode,
                            'module_name' => $moduleName,
                            'value' => explode(" ", str_replace("<br>", " ", $note))[0],
                            'year_start' => $yearStart,
                            'year_end' => $yearEnd,
                        ]);

                        $marks[] = $mark;
                    }
                }
            } catch (\Throwable $th) {
                echo $th->getMessage();
            }
        }
    }
}
