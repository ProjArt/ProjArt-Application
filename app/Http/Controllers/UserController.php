<?php

namespace App\Http\Controllers;

use App\Http\Requests\OneSignalRequest;

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
}
