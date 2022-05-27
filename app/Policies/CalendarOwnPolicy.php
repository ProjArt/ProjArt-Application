<?php

namespace App\Policies;

use App\Models\Calendar;
use App\Models\CalendarOwn;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CalendarOwnPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user != null;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CalendarOwn  $calendarOwn
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CalendarOwn $calendar)
    {
        return $this->isOwner($user, $calendar);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user != null;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CalendarOwn  $calendarOwn
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CalendarOwn $calendar)
    {
        return $this->isOwner($user, $calendar);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CalendarOwn  $calendarOwn
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CalendarOwn $calendar)
    {
        return $this->isOwner($user, $calendar);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CalendarOwn  $calendarOwn
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, CalendarOwn $calendar)
    {
        return $this->isOwner($user, $calendar);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CalendarOwn  $calendarOwn
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, CalendarOwn $calendar)
    {
        return $this->isOwner($user, $calendar);
    }

    private function isOwner(User $user, CalendarOwn $calendar)
    {
        return $user->calendarsOwn->map(function ($calendar) {
            return $calendar->id;
        })->contains($calendar->id);
    }

    public function share(User $user, CalendarOwn $calendar)
    {
        return $this->isOwner($user, $calendar);
    }
}
