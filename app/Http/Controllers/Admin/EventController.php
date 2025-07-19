<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Event::class);

        $events = Event::select('id', 'title', 'description', 'event_date', 'location', 'capacity', 'current_registrations_count', 'status', 'deleted_at', 'created_at', 'updated_at')
            ->withCount('users')
            ->withTrashed()
            ->paginate(10);

        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
        ]);
    }

    public function create()
    {
        Gate::authorize('create', Event::class);

        return Inertia::render('Admin/Events/Create');
    }

   public function store(Request $request)
{
    Gate::authorize('create', Event::class);

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'event_date' => 'required|date|after:now',
        'location' => 'required|string|max:255',
        'capacity' => 'required|integer|min:1',
        'status' => 'required|in:draft,published,cancelled',
    ]);

    $event = Event::create($validated);

    if ($request->wantsJson()) {
        return response()->json([
            'message' => 'Event created successfully',
            'data' => $event,
        ], 201);
    }

    return Redirect::route('admin.events.index')->with('message', 'Event created successfully.');
}

    public function show(Event $event)
    {
        Gate::authorize('view', $event);

        return Inertia::render('Admin/Events/Show', [
            'event' => $event->load('users:id,name,email'),
        ]);
    }

    public function edit(Event $event)
    {
        Gate::authorize('update', $event);

        return Inertia::render('Admin/Events/Edit', [
            'event' => $event->only('id', 'title', 'description', 'event_date', 'location', 'capacity', 'status'),
        ]);
    }

    public function update(Request $request, Event $event)
    {
        Gate::authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,cancelled',
        ]);

        $event->update($validated);

        return Redirect::route('admin.events.index')->with('message', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        Gate::authorize('delete', $event);

        $event->delete();

        return Redirect::route('admin.events.index')->with('message', 'Event deleted successfully.');
    }

    public function restore(Event $event)
    {
        $event = Event::withTrashed()->findOrFail($event->id);
        Gate::authorize('restore', $event);

        $event->restore();

        return Redirect::route('admin.events.index')->with('message', 'Event restored successfully.');
    }

    public function forceDestroy(Event $event)
    {
        Gate::authorize('forceDelete', $event);

        $event->forceDelete();

        return Redirect::route('admin.events.index')->with('message', 'Event permanently deleted.');
    }
}