<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Gate;

class EventRegistrationController extends Controller
{
     public function index(Request $request)
{
    Gate::authorize('viewAny', EventRegistration::class);

    $query = EventRegistration::with(['user:id,name,email', 'event:id,title'])
        ->select('id', 'user_id', 'event_id', 'created_at');

    if ($request->filled('event_id')) {
        $query->where('event_id', $request->event_id);
    }

    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    $registrations = $query->paginate(10)->withQueryString();

    $users = User::select('id', 'name')->get();
    $events = Event::select('id', 'title')->where('status', 'published')->get();

    return Inertia::render('Admin/Registrations/Index', [
        'registrations' => $registrations,
        'users' => $users,
        'events' => $events,
        'filters' => $request->only(['event_id', 'user_id']),
    ]);
}

    public function create()
    {
        Gate::authorize('create', EventRegistration::class);

        $users = User::select('id', 'name')->get();
        $events = Event::select('id', 'title')->where('status', 'published')->get();

        return Inertia::render('Admin/Registrations/Create', [
            'users' => $users,
            'events' => $events,
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', EventRegistration::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
        ]);

        $event = Event::findOrFail($validated['event_id']);

        if ($event->status !== 'published') {
            return Redirect::back()->withErrors(['event_id' => 'Can only register for published events.']);
        }

        if ($event->isFull()) {
            return Redirect::back()->withErrors(['event_id' => 'Event is at full capacity.']);
        }

        if ($event->users()->where('user_id', $validated['user_id'])->exists()) {
            return Redirect::back()->withErrors(['user_id' => 'User is already registered for this event.']);
        }

        EventRegistration::create($validated);
        $event->increment('current_registrations_count');

        return Redirect::route('admin.registrations.index')->with('message', 'Registration created successfully.');
    }

    public function show(EventRegistration $eventRegistration)
    {
        Gate::authorize('view', $eventRegistration);

        return Inertia::render('Admin/Registrations/Show', [
            'registration' => $eventRegistration->load(['user:id,name,email', 'event:id,title']),
        ]);
    }

    public function destroy(EventRegistration $eventRegistration)
    {
        Gate::authorize('delete', $eventRegistration);

        $event = $eventRegistration->event;
        $eventRegistration->delete();
        $event->decrement('current_registrations_count');

        return Redirect::route('admin.registrations.index')->with('message', 'Registration deleted successfully.');
    }

    //Export Registrations in CSV
    public function export()
    {
        $filename = 'registrations_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Event Title', 'User Name', 'User Email', 'Registered At']);

            EventRegistration::with(['event', 'user'])->chunk(100, function ($registrations) use ($handle) {
                foreach ($registrations as $registration) {
                    fputcsv($handle, [
                        $registration->id,
                        $registration->event->title ?? 'N/A',
                        $registration->user->name ?? 'N/A',
                        $registration->user->email ?? 'N/A',
                        $registration->created_at,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, $headers);
    }
}