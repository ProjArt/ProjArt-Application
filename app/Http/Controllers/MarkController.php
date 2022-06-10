<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarkRequest;
use App\Http\Requests\UpdateMarkRequest;
use App\Http\Services\GapsMarksService;
use App\Models\Mark;
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


        $response = Http::withBasicAuth($user->username, $user->password)->asForm()->post('https://gaps.heig-vd.ch/consultation/etudiant/', [
            "rs" => "smartReplacePart",
            "rsargs" => '["STUDENT_SELECT_ID","studentDataDiv","2830971874312375411",null,null,' . $user->gaps_id . ',null]'
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
                $m[$key]["percent"] = intval(($value["weight"] / $total) * 100) . "%";
            }
            return $m;
        });

        return response()->json($modules);

        /* $res = str_replace('\u00a3', '', $response->body());
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

        $res = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $res); 


        return $res;*/
    }
}
