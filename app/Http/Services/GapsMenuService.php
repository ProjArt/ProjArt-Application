<?php

namespace App\Http\Services;

use App\Models\Mark;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;



class GapsMenuService
{
    public static function fetchMenus()
    {
        $response = Http::withToken(config('twitter.token'))
            ->get("https://api.twitter.com/2/users/185659828/tweets", [
                /*  'start_time' => '2022-05-22T00:00:00Z',
                'end_time' => "2022-05-25T00:00:00Z", */
                'max_results' => 10,
            ]);

        collect($response->json()["data"])->map(function ($tweet) {
            $menuDatas = explode("*", $tweet["text"]);

            $menuDatas = [
                "entry" => trim($menuDatas[0]),
                "plate" => trim($menuDatas[1]),
                "dessert" => trim($menuDatas[2]),
            ];

            if (strlen($menuDatas["entry"]) < 5 || strlen($menuDatas["plate"]) < 5) {
                return;
            }

            $menu = Menu::firstOrCreate([
                'id' => $tweet["id"],
            ]);

            return $menu->meals()->firstOrCreate($menuDatas, [
                'menu_id' => $tweet["id"],
            ]);
        });
    }
}
