<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MenuController extends Controller
{
    public function index()
    {
        //$response = Http::withBasicAuth('vincent.tarrit', 'Bc&84NC@jPDb9La6')->get('https://intra.heig-vd.ch/campus/cafeterias/Pages/cafeteria.aspx');

        $response = Http::withToken(config('twitter.token'))
            ->get("https://api.twitter.com/2/users/185659828/tweets", [
                /*  'start_time' => '2022-05-22T00:00:00Z',
                'end_time' => "2022-05-25T00:00:00Z", */
                'max_results' => 10,
            ]);

        $body = collect($response->json()["data"])->reject(function ($tweet) {
            return strlen($tweet["text"])  < 5;
        })->map(function ($tweet) {
            $menu = Menu::firstOrCreate([
                'id' => $tweet["id"],
            ]);
            $menuDatas = explode("*", $tweet["text"]);
            $menuDatas = [
                "entry" => trim($menuDatas[0]),
                "plate" => trim($menuDatas[1]),
                "dessert" => trim($menuDatas[2]),
            ];

            return $menu->meals()->firstOrCreate($menuDatas, [
                'menu_id' => $tweet["id"],
            ]);
        })->groupBy(function ($meal) {
            return \Carbon\Carbon::parse($meal->date)->format("Y-m-d");
        })->toArray();


        return httpSuccess('Menu', $body);
    }
}
