<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendeeGroupSeeder extends Seeder
{
    public function run(): void
    {
        $attendeeGroups = [
            ['name' => 'Speakers', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Attendees', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sponsors', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('attendee_groups')->insert($attendeeGroups);
    }
}
