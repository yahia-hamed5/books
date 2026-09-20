<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create(
            [
                'name' => 'yahia',
                'email' => 'admin@book.com',
                'password' => Hash::make('password_admin'),
                'role'=>'admin'
            ]
        );

         User::factory()->create(
            [
                'name' => 'mohamed',
                'email' => 'mohamed@book.com',
                'password' => Hash::make('mohamed_password'),
                'role'=> 'user'
            ]
        );

         User::factory()->create(
            [
                'name' => 'osama',
                'email' => 'osama@book.com',
                'password' => Hash::make('osama_password'),
                'role'=> 'user'
            ]
        );
    }
}
