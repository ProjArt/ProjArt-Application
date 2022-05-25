<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserCanManageCalendar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, $calendarId, Closure $next)
    {

        $userId = $request->user()->id;
        $manageableCalendarsIds = DB::table('calendar_user_own')
        ->select('calendar_id')
        ->where('user_id', '=', $userId)
        ->get();

        if($manageableCalendarsIds != null)
        {
            return $next($request);
        } else {
            abort(403, "Vous n'avez pas le droit de faire cette opération");
        }       
    }
}
