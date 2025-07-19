<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventRegistrationController;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/admin/dashboard', function () {

    $eventStatusCounts = Event::groupBy('status')
            ->selectRaw('status, COUNT(*) as count')
            ->pluck('count', 'status')
            ->toArray();

     // Calculate event capacity counts
        $eventCapacityCounts = Event::selectRaw('
            SUM(CASE WHEN current_registrations_count >= capacity THEN 1 ELSE 0 END) as full,
            SUM(CASE WHEN current_registrations_count = 0 THEN 1 ELSE 0 END) as empty,
            SUM(CASE WHEN current_registrations_count > 0 AND current_registrations_count < capacity THEN 1 ELSE 0 END) as partial
        ')->first()->toArray();
            

        return Inertia::render('Admin/Dashboard/Index', [
            'metrics' => [
                'total_users' => User::count(),
                'total_events' => Event::count(),
                'total_registrations' => EventRegistration::count(),
            ],
             'eventStatusCounts' => $eventStatusCounts,
             'eventCapacityCounts' => [
                'full' => (int) $eventCapacityCounts['full'],
                'empty' => (int) $eventCapacityCounts['empty'],
                'partial' => (int) $eventCapacityCounts['partial'],
            ],
        ]);
    })->name('dashboard')->middleware(['auth', 'isAdmin']);


Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    // User Management Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->can('viewAny', App\Models\User::class);
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->can('create', App\Models\User::class);
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->can('create', App\Models\User::class);
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show')->can('view', 'user');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->can('update', 'user');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->can('update', 'user');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->can('delete', 'user');

    // Event Management Routes
    Route::get('/events', [EventController::class, 'index'])->name('events.index')->can('viewAny', App\Models\Event::class);
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create')->can('create', App\Models\Event::class);
    Route::post('/events', [EventController::class, 'store'])->name('events.store')->can('create', App\Models\Event::class);
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show')->can('view', 'event');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit')->can('update', 'event');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update')->can('update', 'event');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy')->can('delete', 'event');
    Route::post('/events/{event}/restore', [EventController::class, 'restore'])->name('events.restore')->can('restore', 'event');
    Route::delete('/events/{event}/force', [EventController::class, 'forceDestroy'])->name('events.forceDestroy')->can('forceDelete', 'event');

    // Event Registration Management Routes
    Route::get('/registrations', [EventRegistrationController::class, 'index'])->name('registrations.index')->can('viewAny', App\Models\EventRegistration::class);
    Route::get('/registrations/create', [EventRegistrationController::class, 'create'])->name('registrations.create')->can('create', App\Models\EventRegistration::class);
    Route::post('/registrations', [EventRegistrationController::class, 'store'])->name('registrations.store')->can('create', App\Models\EventRegistration::class);
    Route::get('/registrations/{eventRegistration}', [EventRegistrationController::class, 'show'])->name('registrations.show')->can('view', 'eventRegistration');
    Route::delete('/registrations/{eventRegistration}', [EventRegistrationController::class, 'destroy'])->name('registrations.destroy')->can('delete', 'eventRegistration');
    Route::get('/registrations/export', [EventRegistrationController::class, 'export'])->name('registrations.export')->can('viewAny', App\Models\EventRegistration::class);
});

Route::get('/registrations/export', [EventRegistrationController::class, 'export'])->name('registrations.export');



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
