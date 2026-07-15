<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@ruangbaca.test'],
            [
                'name' => 'Admin RuangBaca',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'blocked_reason' => null,
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'member@ruangbaca.test'],
            [
                'name' => 'Member Contoh',
                'password' => Hash::make('password'),
                'role' => 'member',
                'is_active' => true,
                'blocked_reason' => null,
                'email_verified_at' => now(),
            ],
        );

        $this->call(CatalogSeeder::class);
    }
}
