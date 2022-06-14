<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarkRequest;
use App\Http\Requests\UpdateMarkRequest;
use App\Http\Services\GapsMarksService;
use App\Models\Mark;
use App\Models\MarkModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;

/**
 * @group Notes
 *
 * APIs pour gérer les notes
 */
class MarkController extends Controller
{
    /**
     * 
     * Get marks
     * 
     * Retourne un json contenant une liste des notes de l'utilisateur.
     *
     * 
     * @response scenario=success 
     * 
     * {
     * "2020 - 2021": [
     *   {
     *     "id": 1,
     *     "module_code": "MODULE",
     *     "module_name": "TEXT",
     *     "value": X,
     *     "year_start": X,
     *     "year_end": X
     *   },
     * }
     *    
     * @authenticated
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        /* $marks =  $user->marks->groupBy(function ($mark) {
            return $mark->year_start . " - " . $mark->year_end;
        }); */
        return httpSuccess('Les notes', $user->markmodules);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMarkRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMarkRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function show(Mark $mark)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMarkRequest  $request
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMarkRequest $request, Mark $mark)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mark $mark)
    {
        //
    }

    public function test(Request $request)
    {
        $user = $request->user();

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
                'years' => $this->getSchoolYearsFromDate($m['details'][0]['date']),
                'weight' => 0,
                'weight_percentage' => 0,
                'markmodule_id' => MarkModule::firstOrCreate([
                    'user_id' => $user->id,
                    'code' => 'Controle',
                    'name' => 'Controle Continu',
                    'status' => '',
                    'years' => $this->getSchoolYearsFromDate(date('d.m.Y')),
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

        return $_marks;
    }

    private function getSchoolYearsFromDate($date)
    {
        return "2021 - 2022";
    }
}
