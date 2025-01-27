<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Run the UserSeeder
        $this->call(CompanySeeder::class);
        $this->call(AttendeeGroupSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(TableSeeder::class);
        $this->call(AttendeeEventSeeder::class);
    }
}

