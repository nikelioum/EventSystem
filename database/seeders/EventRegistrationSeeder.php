<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;

class EventRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all regular users and published events
        $users = User::where('role', 'user')->get();
        $events = Event::where('status', 'published')->get();

        foreach ($events as $event) {
            if (!$event->isFull()) {
                // Randomly select 1-5 users, limited by capacity
                $maxRegistrations = min(5, $event->capacity - $event->current_registrations_count);
                if ($maxRegistrations > 0) {
                    $randomUsers = $users->random(rand(1, $maxRegistrations));
                    foreach ($randomUsers as $user) {
                        if (!$event->isFull() && !$event->users()->where('user_id', $user->id)->exists()) {
                            $event->users()->attach($user->id);
                            $event->increment('current_registrations_count');
                        }
                    }
                }
            }
        }
    }
}
