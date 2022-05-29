<?php

namespace App\Policies;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Event $event)
    {
        return $this->canViewEvent($user, $event);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can store the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function store(User $user, Calendar $calendar)
    {
        return $this->checkRightsOnCalendar($user, $calendar, Calendar::EDIT_RIGHT);
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Event $event)
    {
        return $this->canEditEvent($user, $event);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Event $event)
    {
        return $this->canEditEvent($user, $event);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Event $event)
    {
        return $this->canEditEvent($user, $event);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Event $event)
    {
        return $this->canEditEvent($user, $event);
    }

    private function canEditEvent(User $user, Event $event)
    {
        return $this->checkRightsOnCalendar($user, $event->calendar, Calendar::EDIT_RIGHT);
    }

    private function canViewEvent(User $user, Event $event)
    {
        return $this->checkRightsOnCalendar($user, $event->calendar, Calendar::READ_RIGHT);
    }

    private function checkRightsOnCalendar(User $user, Calendar $calendar, $rights)
    {
        $c =  $user->calendars()->whereId($calendar->id)->first();
        return $c != null && $c->pivot->rights == $rights;
    }
}
