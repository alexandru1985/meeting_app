<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            ['name' => 'Unknown', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CompanyA', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CompanyB', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CompanyC', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('companies')->insert($companies);
    }
}
