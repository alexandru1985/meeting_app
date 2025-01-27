<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\User;
use Faker\Factory as Faker;

class AttendeeEventSeeder extends Seeder
{
  public function run(): void
  {
    $events = Event::all();
    $users = User::all();

    $data = [];

    for ($i = 0; $i < 80; $i++) {
      $data[] = [
        'event_id' => $events->random()->id,
        'attendee_id' => $users->random()->id,
        'confirmed' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ];
    }

    DB::table('attendee_event')->insert($data);
  }
}
