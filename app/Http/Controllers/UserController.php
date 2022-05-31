<?php

namespace App\Http\Controllers;

use App\Http\Requests\OneSignalRequest;
use App\Http\Requests\ThemeRequest;
use Illuminate\Http\Request;

/**
 * @group Utilisateurs
 *
 * APIs pour gérer les utilisateurs
 */
class UserController extends Controller
{

    /**
     * 
     * Set onesignal user id
     * 
     * Pour mettre à jour l'id d'un utilisateur
     *
     *    
     * @authenticated
     * @return \Illuminate\Http\Response
     */
    public function setOnesignal(OneSignalRequest $request)
    {
        $user = $request->user();
        $user->update([
            'onesignal_id' => $request->onesignal_id
        ]);
        return response()->json(['success' => true]);
    }

    /**
     * 
     * Set theme
     * 
     * Pour mettre à jour le thème d'un utilisateur
     * 
     */
    public function setTheme(ThemeRequest $request)
    {
        $user = $request->user();
        $user->update([
            'theme_id' => $request->theme_id
        ]);
        return httpSuccess("Le thème a été mis à jour", $user);
    }
}
