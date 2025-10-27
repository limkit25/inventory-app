<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category; // <-- Impor model

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::firstOrCreate(['name' => 'Bahan Medis Habis Pakai (BMHP)'], [
            'description' => 'Barang sekali pakai untuk tindakan medis.'
        ]);

        Category::firstOrCreate(['name' => 'Alat Kesehatan (Alkes)'], [
            'description' => 'Peralatan medis non-habis pakai.'
        ]);

        Category::firstOrCreate(['name' => 'Obat-obatan'], [
            'description' => 'Produk farmasi.'
        ]);

        Category::firstOrCreate(['name' => 'Alat Tulis Kantor (ATK)'], [
            'description' => 'Kebutuhan administrasi kantor.'
        ]);
    }
}