<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Public can view published events via API
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Event $event): bool
    {
        return $event->status === 'published' || ($user && $user->role === 'admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can register for the event.
     */
    public function register(User $user, Event $event): Response
    {
        if ($event->status !== 'published') {
            return Response::deny('Can only register for published events.');
        }

        if ($event->isFull()) {
            return Response::deny('Event is at full capacity.');
        }

        if ($event->users()->where('user_id', $user->id)->exists()) {
            return Response::deny('You are already registered for this event.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can unregister from the event.
     */
    public function unregister(User $user, Event $event): Response
    {
        if ($event->users()->where('user_id', $user->id)->exists()) {
            return Response::allow();
        }

        return Response::deny('You are not registered for this event.');
    }
}