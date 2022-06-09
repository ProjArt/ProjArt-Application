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

                $response = Http::withBasicAuth($user->username, $user->password)->get("https://gaps.heig-vd.ch/consultation/notes/bulletin.php?id=" . $user->gaps_id);

                $dom = HtmlDomParser::str_get_html($response->body());

                $trs = $dom->findMulti("#record_table tr");

                $module = null;

                foreach ($trs as $tr) {

                    try {
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
                            $years = $tds[3]->innerText;
                            $note = $tds[4]->innerText;

                            $mark = $user->marks()->firstOrCreate([
                                'markmodule_id' => $module->id,
                                'course_code' => $courseCode,
                                'course_name' => $courseName,
                                'value' => explode(" ", str_replace("<br>", " ", $note))[0],
                                'years' => $years,
                            ]);

                            $datas = explode("<br>", $mark->course_name)[1];


                            $details = explode("&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;&nbsp;", $datas);


                            foreach ($details as $detail) {
                                $detail = str_replace("<b>", "", $detail);
                                $detail = str_replace("</b>", "", $detail);
                                $detail = str_replace("&nbsp;", "", $detail);

                                $value = explode(" : ", $detail)[1];
                                $title = explode(" (", explode(" : ", $detail)[0])[0];
                                $weight = explode(" ", explode(" : ", $detail)[0])[1];

                                try {
                                    $mark->details()->firstOrCreate([
                                        'title' => $title,
                                        'weight' => $weight,
                                        'value' => $value
                                    ]);
                                } catch (\Exception $e) {
                                    //
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        print_r($e->getMessage());
                    }

                    /* continue;

                    // CONTROLE CONTINU 

                    $response = Http::withBasicAuth($user->username, $user->password)
                        ->asForm()
                        ->post("https://gaps.heig-vd.ch/consultation/controlescontinus/consultation.php?idst=" . $user->gaps_id, [
                            "rs" => "getStudentCCs",
                            "rsargs" => "[17486, 2021, null]"
                        ]);

                    $res =  str_replace('\\', '', $response->body());
                    $res = str_replace('+:"', '', $res);
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

                    $trs = $dom->find("table.displayArray")->findMulti("tr");

                    $course = "";
                    $course_code = "";
                    $course_details = [];

                    $marks = [];

                    foreach ($trs as $tr) {
                        $tds = $tr->findMulti("td");
                        if ($tds[0]->class == "bigheader") {
                            $course = $tds[0]->innerText;
                            $course_code = explode(" - ", $course)[0];
                            $marks[$course_code] = [
                                'course_name' => $course,
                                'course_code' => $course_code,
                            ];
                        }
                        if ($tds[0]->class == "bodyCC") {
                            $course_details = [
                                'date' => $tds[0]->innerText,
                                'name' => $tds[1]->find('div')[0]->innerText,
                                'class_average' => $tds[2]->innerText,
                                'poids' => $tds[3]->innerText,
                                'note' => $tds[4]->innerText,

                            ];
                            $marks[$course_code]['details'][] = $course_details;
                        }
                    } */

                    // pourcentage 

                    $response = Http::withBasicAuth($user->username, $user->password)
                        ->asForm()
                        ->post("https://gaps.heig-vd.ch/consultation/etudiant", [
                            "rs" => "smartReplacePart",
                            "rsargs" => '["STUDENT_SELECT_ID","studentDataDiv","2830971874312375411",null,null,' . $user->gaps_id . ',null]'
                        ]);

                    $res = str_replace('\u00a3', '', $response->body());
                    $res = str_replace('\n', '', $res);
                    $res = str_replace('\r', '', $res);
                    $res = str_replace('\t', '', $res);
                    $res = str_replace('\"', '"', $res);
                    $res = str_replace('@@', '', $res);
                    $res = str_replace('+:"', '', $res);
                    $res = str_replace('&nbsp', '', $res);
                    $res = str_replace('\\', '', $res);
                    $res = str_replace('u00e9', 'é', $res);

                    $res = substr_replace($res, "", -1);
                }
            } catch (\Throwable $th) {
                // echo $th->getMessage();
                return $th->getMessage();
            }
        }
        return "ok";
    }
}
