<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = ['admin', 'artist1', 'artist2', 'artist3', 'artist4', 'artist5'];

        foreach ($users as $user) {
            \App\Models\User::create([
                'name' => $user,
                'email' => $user . '@gmail.com',
                'password' => bcrypt('password'),
            ]);

            if ($user === 'admin') {
                $admin = \App\Models\User::where('name', $user)->first();
                $admin->roles()->attach(\App\Models\Role::where('name', 'admin')->first());
            } else {
                $artist = \App\Models\User::where('name', $user)->first();
                $artist->roles()->attach(\App\Models\Role::where('name', 'artist')->first());
            }
        }
    }
}
