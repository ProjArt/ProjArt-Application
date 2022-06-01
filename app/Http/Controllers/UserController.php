<?php

namespace App\Http\Controllers;

use App\Http\Requests\OneSignalRequest;
use App\Models\User;

/**
 * @group Utilisateurs
 *
 * APIs pour gérer les utilisateurs
 */
class UserController extends Controller
{

    /**
     * 
     * Get users
     * 
     * Pour obtenir une liste de tous les utilisateurs
     */
    public function index()
    {
        $users = User::orderBy('username')->select("id", "username")->get();
        return httpSuccess("Utilisateurs", $users->makeHidden(['theme']));
    }
}
