<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    private const INDUSTRIES = [
        'Software',
        'Manufacturing',
        'Healthcare',
        'Logistics',
        'Retail',
        'Financial Services',
        'Education',
        'Real Estate',
        'Telecommunications',
        'Energy',
        'Construction',
        'Hospitality',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'domain' => fake()->unique()->domainName(),
            'industry' => fake()->randomElement(self::INDUSTRIES),
        ];
    }
}
