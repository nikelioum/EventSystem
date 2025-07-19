<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserEventResource;

class RegistrationsController extends Controller
{
    /**
     * Get all events the authenticated user is registered for.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 5);
        $user = auth()->user();

        $events = $user->events()
            ->where('status', 'published')
            ->paginate($perPage);

        return response()->json([
            'message' => $events->isEmpty() ? 'No registrations found.' : 'Your Registrations',
            'data' => UserEventResource::collection($events->items()),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ],
        ]);
    }

    /**
     * Register the authenticated user for an event.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function register($id)
    {
        $event = Event::where('status', 'published')->find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found or not published'
            ], 404);
        }

        $user = auth()->user();

        if ($event->isFull()) {
            return response()->json([
                'message' => 'Event is full'
            ], 400);
        }

        if ($event->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You are already registered for this event'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $event->users()->attach($user->id);
            $event->increment('current_registrations_count');

            DB::commit();

            return response()->json([
                'message' => 'Successfully registered for the event',
                'data' => new UserEventResource($event)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to register for the event'
            ], 500);
        }
    }

    /**
     * Unregister the authenticated user from an event.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unregister($id)
    {
        $event = Event::where('status', 'published')->find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found or not published'
            ], 404);
        }

        $user = auth()->user();

        if (!$event->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You are not registered for this event'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $event->users()->detach($user->id);
            $event->decrement('current_registrations_count');

            DB::commit();

            return response()->json([
                'message' => 'Successfully unregistered from the event'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to unregister from the event'
            ], 500);
        }
    }
}