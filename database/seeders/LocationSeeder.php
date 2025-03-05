<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $locations = [
            0 => ['name' => 'BallroomA', 'address' => '72 Denton Road, London, N8 9LW, United Kingdom', 'latitude' => '51.580469', 'longitude' => '-0.111925'],
            1 => ['name' => 'BallroomB', 'address' => '30 Burlington Road, London, EN2 0LL, United Kingdom', 'latitude' => '51.664556', 'longitude' => '-0.083859'],
            2 => ['name' => 'BallroomC', 'address' => '2 Hermiston Court, London, N8 8NN, United Kingdom', 'latitude' => '51.584373', 'longitude' => '-0.118792'],
        ];
        

        $data = [];

        for($i = 0; $i <=2; $i++) {
            $data [] = [
                'name' =>  $locations[$i]['name'],
                'address' => $locations[$i]['address'],
                'latitude' => $locations[$i]['latitude'],
                'longitude' => $locations[$i]['longitude'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('locations')->insert($data);
    }
}
