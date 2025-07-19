<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Resources\EventsResource;

class EventsController extends Controller
{
    /**
     * Get all published events with pagination.
     */
    public function index(Request $request)
{
    $perPage = $request->query('per_page', 5);

    $events = Event::where('status', 'published')->paginate($perPage);

    return response()->json([
        'data' => EventsResource::collection($events->items()),
        'pagination' => [
            'current_page' => $events->currentPage(),
            'last_page' => $events->lastPage(),
            'per_page' => $events->perPage(),
            'total' => $events->total(),
        ],
    ]);
}

    /**
     * Get a single event by ID.
     */
    public function show($id)
    {
        $event = Event::where('status', 'published')->find($id);

        if (!$event) {
            return response()->json([
                'message' => 'Event not found or not published',
            ], 404);
        }

        return response()->json([
            'data' => new EventsResource($event),
        ]);
    }
}
