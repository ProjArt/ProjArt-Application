<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnsureUserCanManageCalendar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $userId = $request->user()->id;
        $calendarId = $request->input('calendar_id');
        
        $manageableCalendars = DB::table('calendar_user_own')
        ->where('user_id', '=', $userId)
        ->where('calendar_id', '=', $calendarId)
        ->first();

       
        if($manageableCalendars != null )
        {
            return $next($request);
        } else {
            abort(403, "Vous n'avez pas le droit de faire cette opération");
        }       
    }
}
