<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    private const JOB_TITLES = [
        'Chief Revenue Officer',
        'VP of Sales',
        'Sales Director',
        'Sales Manager',
        'Head of Revenue Operations',
        'Sales Operations Manager',
        'Sales Enablement Manager',
        'Account Executive',
        'Senior Account Executive',
        'SDR Manager',
        'Customer Success Director',
        'IT Manager',
        'Procurement Manager',
        'Finance Manager',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'first_name' => fake()->firstName(),
            'last_name'  => fake()->lastName(),
            'email'      => fake()->unique()->safeEmail(),
            'job_title'  => fake()->randomElement(self::JOB_TITLES),
            'phone'      => fake()->optional()->phoneNumber(),
        ];
    }
}
