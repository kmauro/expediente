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
            ['name' => 'En buzon', 'color' => 'teal'],
            ['name' => 'Pendiente', 'color' => 'yellow'],
            ['name' => 'Urgente', 'color' => 'red'],
            ['name' => 'Enviado', 'color' => 'green'],
            ['name' => 'Finalizado', 'color' => 'mist'],
        ];

        foreach ($states as $state) {
            DB::table('states')->insert($state);
        }

    }
}
