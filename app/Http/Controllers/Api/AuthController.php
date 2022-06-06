<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GapsMarksController;
use App\Http\Requests\AuthUserRequest;
use App\Http\Services\GapsEventsService;
use App\Http\Services\GapsMarksService;
use App\Jobs\DownloadFromGapsJob;
use App\Models\Calendar;
use App\Models\Classroom;
use App\Models\GapsUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @group Authentication
 *
 * APIs pour gérer les authentifications
 */
class AuthController extends Controller
{

    /**
     * register
     * 
     * Register a new user
     * 
     * @bodyParam username string required The Gaps username of the user.
     * @bodyParam password string required The Gaps password of the user.
     * @bodyParam classroom_name string required The class of the user.
     */

    public function register(AuthUserRequest $request)
    {

        if (User::whereUsername(strtolower($request->username))->exists()) {
            return httpError("This username is already taken");
        }

        if (!GapsUser::whereGapsUsername($request->username)->exists()) {
            return httpError("This username is not registered in Gaps");
        }

        if (!$this->credentialsIsValid($request->username, $request->password)) {
            return httpError("This credentials are not valid");
        }

        $data = $request->all();
        $data['username'] = strtolower($request->username);
        $user = User::create($data);
        $token = $user->createToken('auth_token')->plainTextToken;

        if ($request->classroom_name) {
            $user->classrooms()->sync([$request->classroom_name]);
            $calendar = Calendar::whereName($request->classroom_name)->first();
            if ($calendar == null) {
                abort(404, "Calendar not found");
            }
            $user->calendars()->sync([$calendar->id]);
        }

        DownloadFromGapsJob::dispatch($user);


        return httpSuccess("Register validated", [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'loading_url' => route('api.fetch.gaps'),
            'user' => $user
        ]);
    }

    /**
     * login
     * 
     * Authentication for a user
     * 
     * @bodyParam username string required The Gaps username of the user.
     * @bodyParam password string required The Gaps password of the user.
     */
    public function login(AuthUserRequest $request)
    {
        $user = User::whereUsername($request->username)->first();

        if (!$user || ($user->password != $request->password)) { // user not found or password is wrong

            return httpError('Invalid login details');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return httpSuccess("Login validated", [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * me
     * 
     * Get actual authenticate user
     * 
     */
    public function me(Request $request)
    {
        return httpSuccess("User found", [
            'user' => $request->user(),
        ]);
    }


    /**
     * logout
     * 
     * Logout the actual authenticate user
     * 
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return httpSuccess("Logout succeed");
    }

    /**
     * getClassrooms
     * 
     * Get all classrooms
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClassroom()
    {
        $classroom = Classroom::all();

        return httpSuccess("Classroom found", [
            'classroom' => $classroom,
        ]);
    }

    /**
     * setClassrooms
     * 
     * Set a classroom for the actual authenticate user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function setClassroom(Request $request)
    {
        $request->user()->classroom_id = $request->classroom_id;
        $request->user()->save();

        return httpSuccess("Classroom set");
    }

    private function credentialsIsValid($username, $password)
    {
        $gapsUser = GapsUser::whereUsername($username)->first();
        if ($gapsUser == null) {
            return false;
        }

        $response = Http::withBasicAuth($username, $password)->get('https://gaps.heig-vd.ch/consultation/etudiant/');

        if ($response->status() != 200) {
            return false;
        }

        if (substr($response->body(), 3, 8) != "<script>") { // not a logged response
            return false;
        }

        return true;
    }
}
