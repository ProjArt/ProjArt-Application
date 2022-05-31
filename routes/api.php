<?php

use App\Http\Controllers\AbsenceController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GapsAbsenceController;
use App\Http\Controllers\GapsController;
use App\Http\Controllers\GapsEventsController;
use App\Http\Controllers\GapsMarksController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\UserController;
use App\Models\Notification;
use Illuminate\Http\Request;
use Pusher\PushNotifications\PushNotifications;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('/login',  'login')->name('api.login');
    Route::post('/register',  'register')->name('api.register');
});

Route::get("/classrooms", [ClassroomController::class, 'index'])->name("api.classrooms.index");

//Auth routes
Route::middleware('auth:sanctum')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::get('/me', 'me')->name("api.me");
        Route::get('/logout', 'logout')->name("api.logout");
    });

    Route::prefix('/update/gaps')->group(function () {
        Route::get('/events', [GapsEventsController::class, 'fetchAll'])->name('api.fetch.gaps.events');
        Route::get('/marks', [GapsMarksController::class, 'fetchAll'])->name('api.fetch.gaps.marks');
        Route::get('/absences', [GapsAbsenceController::class, 'fetchAll'])->name('api.fetch.gaps.absences');
        Route::get('/all', [GapsController::class, 'updateAll'])->name('api.fetch.gaps');
    });


    Route::prefix('/mails')->controller(MailController::class)->group(function () {
        Route::get('/', 'index')->name('api.mails.index');
        Route::get('/{id}', 'show')->name('api.mails.show')->where('id', '[0-9]+');
        Route::post('/send', 'send')->name('api.mails.send');
    });

    Route::resource('/events', EventController::class, [
        'as' => 'api'
    ]);
    //Route::get('/events/calendar/{calendarId}', [EventController::class, 'getCalendarEvents'])->name("api.getCalendarEvents");


    Route::get('/marks', [MarkController::class, 'index'])->name('api.marks.index');

    Route::get('/menu', [MenuController::class, 'index'])->name("api.getMenu");

    Route::resource('/calendars', CalendarController::class, [
        'as' => 'api'
    ]);

    Route::post("/calendars/import", [GapsEventsController::class, 'importCalendarICS'])->name("api.calendar.import");

    Route::post('/calendars/share', [CalendarController::class, 'share'])->name("api.calendars.share");


    Route::post("/classrooms/setUser", [ClassroomController::class, 'setUserClassroom'])->name("api.classrooms.setUserClassroom");

    Route::get("/absences", [AbsenceController::class, 'index'])->name("api.absences.index");

    Route::post("/onesignal", [UserController::class, 'setOnesignal'])->name("api.onesignal");

    Route::post("/notification", [NotificationController::class, 'send'])->name("api.notification.send");


    Route::get('/pusher/beams-auth', function (Request $request) {
        $username = $request->user()->username; // If you use a different auth system, do your checks here
        $beamsClient = new PushNotifications([
            "instanceId" => config('services.pusher.beams_instance_id'),
            "secretKey" => config('services.pusher.beams_secret_key'),
        ]);
        $beamsToken = $beamsClient->generateToken($username);
        return response()->json($beamsToken);
    });

    Route::post("/beams", function (Request $request) {
        $beamsClient = new PushNotifications([
            "instanceId" => config('services.pusher.beams_instance_id'),
            "secretKey" => config('services.pusher.beams_secret_key'),
        ]);
        $publishResponse = $beamsClient->publishToUsers(
            ["vincent.tarrit"],
            [
                "apns" => [
                    "aps" => [
                        "alert" => "Hello!",
                    ],
                ],
                "fcm" => [
                    "notification" => [
                        "title" => "Hello!",
                        "body" => "Hello, world!",
                    ],
                ],
            ]
        );

        return response()->json($publishResponse);
    });
});

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to API']);
});


Route::get('/update/gaps/{token}', [GapsController::class, "updateAllCron"])->where('token', config('gaps.token'));

Route::post("/telegram/{token}", [TelegramController::class, "handle"])->where(["token" => env("TELEGRAM_BOT_TOKEN")]);
