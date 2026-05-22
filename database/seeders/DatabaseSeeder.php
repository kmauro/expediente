<?php

namespace Database\Seeders;

use App\Domains\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
        ]);

        User::create([

            'name' => 'Admin',
            'surname' => '',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'role_id' => 1,
            'password' => bcrypt('1q2w3e4r%T')

        ]);
}
}
