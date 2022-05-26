<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @group Menu de la caf
 *
 * APIs pour gérer les menus
 */
class MenuController extends Controller
{
    /**
     * 
     * Obtenir tous les menus
     * 
     * Retourne un json contenant une liste des menus.
     * 
     * @response scenario=success [
     *  {
     *   "2022-05-25": [
     *     {
     *       "id": 1,
     *       "entry": "Crème de légumes",
     *       "plate": "Quiche aux tomates et basilic",
     *       "dessert": "Salade de fruits",
     *       "date": "2022-05-25 23:32:08",
     *       "menu": {
     *         "id": 1529386802506784773,
     *         "date": "2022-05-25 23:32:08"
     *       }
     *     },
     *     {
     *       "id": 2,
     *       "entry": "Crème de légumes",
     *       "plate": "Ailerons de poulet (CH) grillés, Sauce barbecue, Frites, Sauté de légumes de saison",
     *       "dessert": "Salade de fruits",
     *       "date": "2022-05-25 23:32:08",
     *       "menu": {
     *         "id": 1529386800858468355,
     *         "date": "2022-05-25 23:32:08"
     *       }
     *     }
     *   ],
     * }
     *  
     *    
     * @authenticated
     * @return \Illuminate\Http\Response
     */
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
