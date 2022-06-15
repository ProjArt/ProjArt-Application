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
        $year = date('Y');
        $notes = $this->user->marks()->where('years', "=", $year - 1 . " - " . $year)->orWhere('years', '=', $year . " - " . $year + 1)->orderBy('markmodule_id')->get();
        return $this->displayNotes($notes);
    }

    private function displayNotes($notes)
    {
        $s = "";
        foreach ($notes as $note) {
            $s .= "{$note->course_code} : <b>{$note->value}</b> \n";
            //$s .= "<strong>" . $note->module_code . ": </strong>" . strval($note->note) . "\n";
        }
        return $s;
    }

    public static function fetchAllNotes($user = null)
    {

        $users = $user != null ? [$user] : User::all();

        foreach ($users as $user) {

            if ($user->role != User::ROLE_STUDENT) {
                continue;
            }

            $user->markmodules()->delete();

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
                                'weight' =>  0,
                                'weight_percentage' => 0,
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
                }

                // pourcentage 


                $response = Http::withBasicAuth($user->username, $user->password)->asForm()->post('https://gaps.heig-vd.ch/consultation/etudiant/', [
                    "rs" => "smartReplacePart",
                    "rsargs" => '["STUDENT_SELECT_ID","studentDataDiv",null,null,null,' . $user->gaps_id . ',null]'
                ]);

                $res = $response->body();

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

                $trs = $dom->find("div#the_div_2")[0]->findMulti("tr");

                $module = [];
                $module_code = "";
                $lesson = "";
                $weightRaw = 0;

                foreach ($trs as $tr) {

                    if ($tr->hasAttribute("id") && $tr->getAttribute("id") == "uniteVirtuelle") {
                        continue;
                    }


                    $tds = $tr->findMulti("td");
                    foreach ($tds as $td) {
                        if ($td->hasAttribute('colspan')) {
                            if ($td->getAttribute('colspan') == 100) {
                                $module_code = explode(")", explode("(", $td->find("a")[0]->innertext)[1])[0];
                                $lesson = ""; //to clean new line
                            }
                        }

                        if ($td->hasAttribute('style')) {
                            if (str_contains($td->getAttribute('style'), "padding-left:3px; padding-top:2px; padding-bottom:2px;")) {
                                $lesson = explode(")", explode("(", $td->find("a")[0]->innertext)[1])[0];
                            }
                        }

                        if ($td->hasAttribute('colspan')) {
                            if ($td->getAttribute('colspan') == 2) {
                                if (($td->innerText)) {
                                    $weightRaw = $td->innertext;
                                }
                            }
                        }
                    }

                    if ($lesson && $weightRaw) {
                        $module[$module_code][] = [
                            "lesson" => $lesson,
                            "weight" => $weightRaw
                        ];
                    }
                }

                $modules = collect($module)->map(function ($m) {
                    $total = collect($m)->sum("weight");
                    foreach ($m as $key => $value) {
                        $m[$key]["percent"] = intval(($value["weight"] / $total) * 100);
                    }
                    return $m;
                });

                foreach ($modules as $module) {
                    foreach ($module as $lesson) {
                        $mark = $user->marks()->where('course_code', $lesson["lesson"])->first();
                        if ($mark) {
                            $mark->update([
                                'weight' => $lesson['weight'],
                                'weight_percentage' => $lesson['percent']
                            ]);
                        }
                    }
                }
            } catch (\Throwable $th) {
                // echo $th->getMessage();
                return $th->getMessage();
            }


            // CONTROLE CONTINU 

            $response = Http::withBasicAuth($user->username, $user->password)
                ->asForm()
                ->post("https://gaps.heig-vd.ch/consultation/controlescontinus/consultation.php?idst=" . $user->gaps_id, [
                    "rs" => "getStudentCCs",
                    "rsargs" => "[" . $user->gaps_id . ", 2021, null]"
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
                        'name' => explode(";[...]", preg_replace('/<[^>]*>/', '', $tds[1]->find('div')[0]->innerText))[0],
                        'class_average' => $tds[2]->innerText,
                        'poids' => $tds[3]->innerText,
                        'note' => $tds[4]->innerText,

                    ];
                    $marks[$course_code]['details'][] = $course_details;
                }
            }

            $marks = array_values($marks);

            $_marks = [];

            foreach ($marks as $key => $m) {
                $mark =  $user->marks()->firstOrCreate([
                    'course_code' => $m['course_code'],
                ], [
                    'course_code' => $m['course_code'],
                    'course_name' => $m['course_name'],
                    'value' => explode(" : ", $m['course_name'])[1],
                    'years' => self::getSchoolYearsFromDate($m['details'][0]['date']),
                    'weight' => 0,
                    'weight_percentage' => 0,
                    'markmodule_id' => MarkModule::firstOrCreate([
                        'user_id' => $user->id,
                        'code' => 'Contrôles continus',
                        'name' => 'Contrôles continus',
                        'status' => '',
                        'years' => self::getSchoolYearsFromDate(date('d.m.Y')),
                        'mark' => 0,
                        'credits' => 0
                    ])->id,
                ]);

                foreach ($m['details'] as $detail) {
                    $mark->details()->firstOrCreate([
                        'title' => $detail['name'],
                        'weight' => explode(" ", $detail['poids'])[1],
                        'value' => $detail['note'],
                    ]);
                }

                $_marks[] = $mark;
            }
        }
        return "ok";
    }

    private static function getSchoolYearsFromDate($date)
    {
        return "2021 - 2022";
    }
}
