<?php

namespace App\Policies;

use App\Models\Calendar;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CalendarPolicy
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
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Calendar $calendar)
    {
        return $this->isFollower($user, $calendar);
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
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Calendar $calendar)
    {
        return $this->isOwner($user, $calendar);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Calendar $calendar)
    {
        return $this->isOwner($user, $calendar);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Calendar $calendar)
    {
        return $this->isOwner($user, $calendar);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Calendar  $calendar
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Calendar $calendar)
    {
        return $this->isOwner($user, $calendar);
    }

    public function share(User $user, Calendar $calendar)
    {
        return $this->isOwner($user, $calendar);
    }

    private function isOwner(User $user, Calendar $calendar)
    {
        return $this->checkRightsOnCalendar($user, $calendar, Calendar::EDIT_RIGHT);
    }

    private function isFollower(User $user, Calendar $calendar)
    {
        return $this->checkRightsOnCalendar($user, $calendar, Calendar::READ_RIGHT) || $this->isOwner($user, $calendar);
    }

    private function checkRightsOnCalendar(User $user, Calendar $calendar, $rights)
    {
        $c =  $user->calendars()->whereId($calendar->id)->first();
        return $c != null && $c->pivot->rights == $rights;
    }
}
