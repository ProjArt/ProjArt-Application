<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MenuController extends Controller
{
    public function index()
    {
        //$response = Http::withBasicAuth('vincent.tarrit', 'Bc&84NC@jPDb9La6')->get('https://intra.heig-vd.ch/campus/cafeterias/Pages/cafeteria.aspx');
        $response = Http::withBasicAuth('vincent.tarrit', '')->get('https://intra.heig-vd.ch/Pages/home.aspx');
        $body = $response->body();
        return httpSuccess("Menu fetched", $body);
    }
}
