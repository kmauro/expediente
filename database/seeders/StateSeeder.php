<?php

namespace Database\Seeders;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['name' => ''],
            ['color' => ''],
        ];

        foreach ($states as $state) {
            DB::table('states')->insert($state);
        }

    }
}
