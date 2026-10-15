<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Contact;
use App\Models\Segment;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const TAGS = [
        'VIP', 'Decision Maker', 'Champion', 'Churn Risk',
        'Expansion', 'Inbound', 'Outbound', 'Event Lead',
    ];

    private const SEGMENTS = [
        'Enterprise' => 'Accounts with more than 1,000 employees',
        'Mid-Market' => 'Accounts with 100 to 1,000 employees',
        'SMB' => 'Accounts with fewer than 100 employees',
        'Active Pipeline' => 'Contacts with an open opportunity',
        'Nurture' => 'Contacts not ready to buy yet',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        fake()->seed(20261001);

        foreach (self::TAGS as $name) {
            Tag::create(['name' => $name]);
        }

        foreach (self::SEGMENTS as $name => $description) {
            Segment::create(['name' => $name, 'description' => $description]);
        }

        $accounts = Account::factory()->count(50)->create();

        $contacts = [];
        for ($i = 0; $i < 300; $i++) {
            $accountId = fake()->boolean(5)
                ? null
                : fake()->randomElement($accounts->all())->id;

            $contacts[] = Contact::factory()->create(['account_id' => $accountId]);
        }

        $tagIds = Tag::orderBy('id')->pluck('id')->all();
        $segmentIds = Segment::orderBy('id')->pluck('id')->all();

        foreach ($contacts as $contact) {
            $contact->tags()->attach(fake()->randomElements($tagIds, fake()->numberBetween(0, 3)));
            $contact->segments()->attach(fake()->randomElements($segmentIds, fake()->numberBetween(0, 3)));
        }
    }
}
