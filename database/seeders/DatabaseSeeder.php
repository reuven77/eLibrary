<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin RuangBaca',
            'email' => 'admin@ruangbaca.test',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name' => 'Member Contoh',
            'email' => 'member@ruangbaca.test',
            'password' => 'password',
        ]);

        $this->call(CatalogSeeder::class);
    }
}
