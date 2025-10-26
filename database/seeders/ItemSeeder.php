<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item; // <-- Impor model

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Stok untuk Poli Gigi
        Item::firstOrCreate(
            ['name' => 'Masker Medis (Box)'],
            ['unit' => 'box', 'sku' => 'MSK-001']
        );

        Item::firstOrCreate(
            ['name' => 'Sarung Tangan Karet (Box)'],
            ['unit' => 'box', 'sku' => 'GLV-001']
        );

        Item::firstOrCreate(
            ['name' => 'Bahan Tambal Gigi Komposit'],
            ['unit' => 'pcs', 'sku' => 'GIGI-001']
        );

        Item::firstOrCreate(
            ['name' => 'Kapas Dental'],
            ['unit' => 'roll', 'sku' => 'KPS-001']
        );
        
        Item::firstOrCreate(
            ['name' => 'Alkohol 70% (Botol)'],
            ['unit' => 'btl', 'sku' => 'ALK-001']
        );
    }
}