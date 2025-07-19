<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 15 events with varied statuses
        Event::factory()->count(15)->create([
            'status' => fn () => fake()->randomElement(['draft', 'published', 'cancelled']),
        ]);
    }
}
