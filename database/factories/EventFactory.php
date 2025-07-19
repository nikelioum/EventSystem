<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'event_date' => fake()->dateTimeBetween('now', '+1 year'),
            'location' => fake()->city(),
            'capacity' => fake()->numberBetween(10, 100),
            'current_registrations_count' => 0,
            'status' => fake()->randomElement(['draft', 'published', 'cancelled']),
        ];
    }
}
