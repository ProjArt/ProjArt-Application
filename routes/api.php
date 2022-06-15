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
use App\Http\Controllers\GapsUsersController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\LogAfterRequest;
use App\Http\Services\GapsUsersService;

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

Route::get("/getRole/{username}", [UserController::class, 'getRole'])->name("api.getRole");

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

    Route::post("/notification", [NotificationController::class, 'send'])->name("api.notification.send");

    Route::get("/user/notifications", [NotificationController::class, 'getUserNotifications'])->name("api.user.notifications");

    Route::controller(ThemeController::class)->group(function () {
        Route::post("/user/theme", 'setTheme')->name("api.user.setTheme");
        Route::get('/themes', 'index')->name('api.themes.index');
    });

    Route::get("/users", [UserController::class, 'index'])->name("api.users.index");

    Route::delete("/user", [UserController::class, 'destroy'])->name("api.users.destroy");

    Route::get('/gapsUsers/profs', [GapsUsersController::class, 'getProfessorsMySection'])->name('api.gapsUsers.profs');
    Route::get('/gapsUsers/students', [GapsUsersController::class, 'getStudentsMyCourses'])->name('api.gapsUsers.profs');

    Route::get("/test", [MarkController::class, 'test'])->name("api.test");
});

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to API']);
});


Route::get('/update/gaps/{token}', [GapsController::class, "updateAllCron"])->where('token', config('gaps.token'));

Route::post("/telegram/{token}", [TelegramController::class, "handle"])->where(["token" => env("TELEGRAM_BOT_TOKEN")]);

Route::get('/update/gaps/users', function () {
    return GapsUsersService::fetchAllUsers();
});
