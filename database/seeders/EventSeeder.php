<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Event::create([
            'location_id' => 1,
            'name' => 'EventA',
            'start_time' => Carbon::create(2025, 8, 3, 18, 0)->format('Y-m-d H:i'),
            'end_time' => Carbon::create(2025, 8, 3, 20, 0)->format('Y-m-d H:i'),
            'is_set' => 1
        ]);

        Event::create([
            'location_id' => 2,
            'name' => 'EventB',
            'start_time' => Carbon::create(2025, 8, 5, 15, 0)->format('Y-m-d H:i'),
            'end_time' => Carbon::create(2025, 8, 5, 18, 0)->format('Y-m-d H:i'),
            'is_set' => 0
        ]);

        Event::create([
            'location_id' => 3,
            'name' => 'EventC',
            'start_time' => Carbon::create(2025, 10, 15, 19, 0)->format('Y-m-d H:i'),
            'end_time' => Carbon::create(2025, 10, 15, 21, 0)->format('Y-m-d H:i'),
            'is_set' => 0
        ]);

        Event::create([
            'location_id' => 1,
            'name' => 'EventD',
            'start_time' => Carbon::create(2025, 10, 4, 18, 0)->format('Y-m-d H:i'),
            'end_time' => Carbon::create(2025, 10, 4, 21, 0)->format('Y-m-d H:i'),
            'is_set' => 0
        ]);

        Event::create([
            'location_id' => 2,
            'name' => 'EventE',
            'start_time' => Carbon::create(2025, 11, 14, 17, 0)->format('Y-m-d H:i'),
            'end_time' => Carbon::create(2025, 11, 14, 20, 0)->format('Y-m-d H:i'),
            'is_set' => 0
        ]);

        Event::create([
            'location_id' => 3,
            'name' => 'EventF',
            'start_time' => Carbon::create(2025, 11, 20, 18, 0)->format('Y-m-d H:i'),
            'end_time' => Carbon::create(2025, 11, 20, 21, 0)->format('Y-m-d H:i'),
            'is_set' => 0
        ]);
    }
}
