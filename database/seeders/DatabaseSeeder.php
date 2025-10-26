<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Hapus/komentari factory user bawaan
        // User::factory(10)->create();

        // PANGGIL USERSEEDER KITA DI SINI
        $this->call([
            UserSeeder::class,
            VendorSeeder::class, // <-- TAMBAHKAN INI
            ItemSeeder::class,   // <-- TAMBAHKAN INI
        ]);
    }
}
