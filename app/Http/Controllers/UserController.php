<?php

namespace App\Http\Controllers;

use App\Http\Requests\OneSignalRequest;
use App\Models\GapsUser;
use App\Models\User;
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
     * Get users
     * 
     * Pour obtenir une liste de tous les utilisateurs
     */
    public function index()
    {
        $users = User::orderBy('username')->select("id", "username")->get();
        return httpSuccess("Utilisateurs", $users->makeHidden(['theme'])->append(['is_shareable']));
    }

    /**
     * 
     * Get user role
     * 
     * Pour obtenir les informations d'un utilisateur de Gaps
     * 
     * @urlParam id required The id of the user.
     */
    public function getRole($username)
    {
        $gapsUser = GapsUser::whereUsername($username)->first();
        if ($gapsUser == null) {
            return httpError("Utilisateur non trouvé");
        }
        return httpSuccess("Utilisateur", $gapsUser);
    }

    /**
     * 
     * Delete user account
     * 
     * Pour supprimer un compte utilisateur
     */
    public function deleteAccount(Request $request)
    {
        $request->user()->delete();
        return httpSuccess("Compte utilisateur supprimé");
    }
}
