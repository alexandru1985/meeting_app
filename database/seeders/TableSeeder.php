<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tables = [
            ['name' => 'Table1', 'seats' => 10, 'location_id' => 1],
            ['name' => 'Table2', 'seats' => 10, 'location_id' => 1],
            ['name' => 'Table3', 'seats' => 10, 'location_id' => 1],
            ['name' => 'Table4', 'seats' => 10, 'location_id' => 1],
            ['name' => 'Table5', 'seats' => 8, 'location_id' => 1],
            ['name' => 'Table6', 'seats' => 8, 'location_id' => 1],
            ['name' => 'Table7', 'seats' => 8, 'location_id' => 1],
            ['name' => 'Table1', 'seats' => 8, 'location_id' => 2],
            ['name' => 'Table2', 'seats' => 8, 'location_id' => 2],
            ['name' => 'Table3', 'seats' => 8, 'location_id' => 2],
            ['name' => 'Table4', 'seats' => 8, 'location_id' => 2],
            ['name' => 'Table5', 'seats' => 12, 'location_id' => 2],
            ['name' => 'Table6', 'seats' => 12, 'location_id' => 2],
            ['name' => 'Table7', 'seats' => 12, 'location_id' => 2],
            ['name' => 'Table1', 'seats' => 10, 'location_id' => 3],
            ['name' => 'Table2', 'seats' => 10, 'location_id' => 3],
            ['name' => 'Table3', 'seats' => 10, 'location_id' => 3],
            ['name' => 'Table4', 'seats' => 12, 'location_id' => 3],
            ['name' => 'Table5', 'seats' => 12, 'location_id' => 3],
            ['name' => 'Table6', 'seats' => 12, 'location_id' => 3],
        ];

        DB::table('tables')->insert($tables);
    }
}
