<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Support',
            'email' => 'catalog.support@rteam.club'
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'catalog.admin@rteam.club'
        ]);

        User::factory()->create([
            'name' => 'Manager',
            'email' => 'catalog.manager@rteam.club'
        ]);
    }
}
