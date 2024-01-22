<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ["admin", "artist"];

        foreach ($roles as $role) {
            \App\Models\Role::create([
                'name' => $role
            ]);
        }
    }
}
