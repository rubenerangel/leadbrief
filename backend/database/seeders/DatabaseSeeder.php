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

    private const STAGE_ORDER = [
        'discovery', 'demo', 'proposal', 'negotiation',
        'closed_won', 'closed_lost', 'nurture',
    ];

    private const JOURNEYS = [
        ['discovery'],
        ['discovery', 'demo'],
        ['discovery', 'demo', 'proposal'],
        ['discovery', 'demo', 'proposal', 'negotiation'],
        ['discovery', 'demo', 'proposal', 'negotiation', 'closed_won'],
        ['discovery', 'demo', 'proposal', 'negotiation', 'closed_lost'],
        ['discovery', 'demo', 'closed_lost'],
        ['discovery', 'nurture'],
        ['discovery', 'demo', 'closed_lost', 'nurture'],
    ];

    private const ACTIVITY_COUNTS = [1, 2, 2, 3, 3, 4, 5, 6, 8, 10, 12, 15, 20];

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

        $bank = json_decode(
            file_get_contents(database_path('seeders/Data/sales_notes.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
        $notesByStage = collect($bank['notes'])->groupBy('stage');

        $this->command->info('Notes loaded: ' . count($bank['notes']));
        $this->command->info(json_encode($notesByStage->map->count()));

        $total = 0;

        foreach ($contacts as $contact) {
            $journey = fake()->randomElement(self::JOURNEYS);
            $candidates = collect($journey)
                ->flatMap(fn ($stage) => $notesByStage[$stage])
                ->all();
            $count = fake()->randomElement(self::ACTIVITY_COUNTS);
            $picked = fake()->randomElements($candidates, $count);

            usort($picked, fn ($a, $b) =>
                [array_search($a['stage'], self::STAGE_ORDER), $a['id']]
                <=> [array_search($b['stage'], self::STAGE_ORDER), $b['id']]
            );

            $dates = [];
            foreach ($picked as $note) {
                $dates[] = fake()->dateTimeBetween('2025-04-01', '2026-10-01');
            }
            sort($dates);
            $replacements = [
                '{first_name}' => $contact->first_name,
                '{last_name}'  => $contact->last_name,
                '{company}'    => $contact->account?->name ?? 'their company',
                '{job_title}'  => $contact->job_title,
            ];

            foreach ($picked as $i => $note) {
                $contact->activities()->create([
                    'type'        => $note['type'],
                    'subject'     => $note['subject'] === null ? null : strtr($note['subject'], $replacements),
                    'body'        => strtr($note['body'], $replacements),
                    'occurred_at' => $dates[$i],
                ]);
            }

            $total += count($picked);
        }

        $this->command->info('Activities created: ' . $total);
    }
}