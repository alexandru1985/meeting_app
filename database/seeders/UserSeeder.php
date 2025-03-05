<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'company_id' => random_int(1, 4),
            'attendee_group_id' => random_int(1, 3),
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin')
        ]);

        $faker = Faker::create();
        $users = [];
    
        for ($i = 1; $i <= 105; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $email = strtolower($firstName) . '.' . strtolower($lastName) . '@mail.com';
            $users[] = [
                'name' => $firstName . ' ' . $lastName,
                'company_id' => random_int(1, 4),
                'attendee_group_id' => random_int(1, 3),
                'email' => $email,
                'email_verified_at' => now(),
                'password' => null,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    
        DB::table('users')->insert($users);
    }
}
