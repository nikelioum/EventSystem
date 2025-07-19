<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Set time zone to EEST (Europe/Bucharest)
    config(['app.timezone' => 'Europe/Bucharest']);
});

it('allows admin to create an event', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->postJson('/admin/events', [
        'title' => 'Test Event',
        'description' => 'This is a test event',
        'event_date' => '2025-08-01T15:00:00.000000Z',
        'location' => 'Athens',
        'capacity' => 50,
        'status' => 'published',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Event created successfully',
            'data' => [
                'title' => 'Test Event',
                'event_date' => '2025-08-01T15:00:00.000000Z',
                'location' => 'Athens',
                'capacity' => 50,
                'status' => 'published',
            ],
        ])
        ->assertJsonMissing(['created_at', 'updated_at', 'deleted_at']);

    $this->assertDatabaseHas('events', [
        'title' => 'Test Event',
        'location' => 'Athens',
        'capacity' => 50,
    ]);
});

it('prevents non-admin from creating an event', function () {
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user);

    $response = $this->postJson('/admin/events', [
        'title' => 'Blocked Event',
        'description' => 'Should not be created',
        'event_date' => '2025-08-01T15:00:00.000000Z',
        'location' => 'Athens',
        'capacity' => 50,
        'status' => 'published',
    ]);

    $response->assertStatus(403);

    $this->assertDatabaseMissing('events', [
        'title' => 'Blocked Event',
    ]);
});

it('prevents guest from creating an event', function () {
    $response = $this->postJson('/admin/events', [
        'title' => 'Guest Event',
        'description' => 'Should not be created',
        'event_date' => '2025-08-01T15:00:00.000000Z',
        'location' => 'Athens',
        'capacity' => 50,
        'status' => 'published',
    ]);

    $response->assertStatus(401);
});

it('allows user to view published events via API', function () {
    // Create 3 published and 1 draft event
    Event::factory()->count(3)->create(['status' => 'published']);
    Event::factory()->create(['status' => 'draft']);

    // Call API with per_page=2 pagination
    $response = $this->getJson('/api/events?per_page=2');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'event_date',
                    'location',
                    'capacity',
                    'current_registrations_count',
                    'status',
                ],
            ],
            'pagination' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
        ])
        ->assertJsonCount(2, 'data') // Ensure pagination respects per_page=2
        ->assertJsonFragment(['status' => 'published'])
        ->assertJsonMissing(['status' => 'draft']);
});

it('allows user to register for an event via API', function () {
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user);
    $event = Event::factory()->create([
        'status' => 'published',
        'capacity' => 10,
        'current_registrations_count' => 0,
    ]);

    $response = $this->postJson("/api/events/{$event->id}/register");

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Successfully registered for the event',
            'data' => [
                'title' => $event->title,
                'location' => $event->location,
                // Match exact ISO 8601 UTC date format with microseconds & 'Z'
                'event_date' => Carbon::parse($event->event_date)->toISOString(),
            ],
        ])
        ->assertJsonMissing(['deleted_at']);

    $this->assertDatabaseHas('event_registrations', [
        'user_id' => $user->id,
        'event_id' => $event->id,
    ]);

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'current_registrations_count' => 1,
    ]);
});

it('prevents registration for a full event', function () {
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user);
    $event = Event::factory()->create([
        'status' => 'published',
        'capacity' => 5,
        'current_registrations_count' => 5,
    ]);

    $response = $this->postJson("/api/events/{$event->id}/register");

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Event is full',
        ]);

    $this->assertDatabaseMissing('event_registrations', [
        'user_id' => $user->id,
        'event_id' => $event->id,
    ]);
});

it('tests Event model relationship with users', function () {
    $event = Event::factory()->create();
    $user = User::factory()->create();

    $event->users()->attach($user->id);

    expect($event->users)->toHaveCount(1)
        ->and($event->users->first()->id)->toBe($user->id)
        ->and($event->users->first()->pivot->user_id)->toBe($user->id)
        ->and($event->users->first()->pivot->event_id)->toBe($event->id)
        ->and($event->users->first()->pivot->created_at)->not->toBeNull();
});
