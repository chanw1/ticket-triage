<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use Illuminate\Support\Str;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        // Insert 25 fake tickets
        \App\Models\Ticket::factory()->count(25)->create();

        // Or manually create some variations:
        Ticket::create([
            'id' => (string) Str::ulid(),
            'subject' => 'Customer cannot log in',
            'body' => 'A user reported their login is failing with a 403 error.',
            'status' => 'open',
            'category' => null,
            'note' => 'Waiting for AI classification',
        ]);

        Ticket::create([
            'id' => (string) Str::ulid(),
            'subject' => 'Billing discrepancy',
            'body' => 'Customer was charged twice for their subscription.',
            'status' => 'in_progress',
            'category' => 'billing',
            'note' => 'Finance team notified',
        ]);

        Ticket::create([
            'id' => (string) Str::ulid(),
            'subject' => 'App keeps crashing',
            'body' => 'Several users report the mobile app crashes on iOS 18.',
            'status' => 'closed',
            'category' => 'technical',
            'note' => 'Patched in version 1.0.3',
        ]);
    }
}