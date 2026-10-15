<?php

namespace Database\Factories;

use App\Contacts\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contact_id' => Contact::factory(),
            'type' => fake()->randomElement(ActivityType::cases()),
            'subject' => fake()->optional()->sentence(),
            'body' => fake()->paragraph(),
            'occurred_at' => fake()->dateTimeBetween('2025-04-01', '2026-10-01'),
        ];
    }
}
