<?php

namespace App\Http\Services;

use App\Models\Absence;
use App\Models\Course;
use App\Models\GapsUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;

class GapsUsersService
{
    public static function fetchAllUsers()
    {

        if (config('database.default') == "mysql") {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        DB::table('gaps_users')->truncate();
        DB::table('courses')->truncate();

        if (config('database.default') == "mysql") {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }


        $response = Http::withBasicAuth(config('gaps.username'), config('gaps.password'))->asForm()->post('https://gaps.heig-vd.ch/consultation/enseignements/index.php', [
            "cboAnnee" => "2021",
            "cboTrimestre" => 4
        ]);

        $dom = HtmlDomParser::str_get_html($response->body());

        $tables = $dom->findMulti('table[cellpading="3"]');


        $lessons = [];

        $index = 0;
        foreach ($tables as $table) {
            $trs = $table->findMulti('tr');

            // lessons
            $lessonRaw = $trs[0]->findMulti('td');

            $code = $lessonRaw[0]->innerText;
            $name = explode(",", $lessonRaw[1]->innerText)[0];
            $arr = explode("(", $lessonRaw[1]->innerText);
            $faculty = explode(")", end($arr))[0];

            $course = Course::firstOrCreate([
                "code"  => $code,
            ], [
                "name" => $name,
                "faculty" => $faculty,
            ]);


            // users

            $users = [];

            for ($i = 1; $i < count($trs); $i++) {
                $_tds = $trs[$i]->findMulti('td');
                $tds = [];
                foreach ($_tds as $td) {
                    array_unshift($tds, $td);
                }
                $u_name = $tds[1]->innerText;
                $u_mail = $tds[0]->innerText;
                $u_username = explode("@", $u_mail)[0];
                $u_is_prof = $tds[0]->style == "background-color: wheat";
                GapsUser::firstOrCreate([
                    'username' => $u_username,
                ], [
                    'username' => strtolower($u_username),
                    'firstname' => explode(" ", $u_name)[0],
                    'name' => explode(" ", $u_name)[1],
                    'mail' => strtolower($u_mail),
                    'is_teacher' => $u_is_prof,
                ]);

                try {
                    $course->gapsUsers()->attach($u_username);
                } catch (\Exception $e) {
                    //
                }
            }
        }


        return $lessons;
    }
}
