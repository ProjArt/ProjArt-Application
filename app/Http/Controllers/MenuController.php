<?php

namespace App\Http\Controllers;

use App\Http\Services\GapsMenuService;
use App\Models\Meal;
use App\Models\Menu;
use App\Models\Notification;
use Carbon\Carbon;
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
     * Get cafeteria menu
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
        $meals = Meal::join('menus', 'menus.id', '=', 'meals.menu_id')->orderBy('date', 'desc')
            ->get()
            ->groupBy(function ($meal) {
                return \Carbon\Carbon::parse($meal->date)->format("Y-m-d");
            })->toArray();



        return httpSuccess('Menu', $meals);
    }
}
