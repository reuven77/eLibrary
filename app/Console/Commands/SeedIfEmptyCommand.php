<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Command;

class SeedIfEmptyCommand extends Command
{
    protected $signature = 'ruangbaca:seed-if-empty';

    protected $description = 'Isi akun demo + katalog hanya jika database masih kosong';

    public function handle(): int
    {
        $users = User::query()->count();
        $books = Book::query()->count();

        if ($users > 0 && $books > 0) {
            $this->info("Database sudah berisi (users={$users}, books={$books}); skip seed.");

            return self::SUCCESS;
        }

        $this->warn("Database kosong/parsial (users={$users}, books={$books}); menjalankan seeder demo…");
        $this->call('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true]);
        $this->info('Seeder selesai. Login: admin@ruangbaca.test / password');

        return self::SUCCESS;
    }
}
