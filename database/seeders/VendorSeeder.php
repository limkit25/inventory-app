<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor; // <-- Impor model

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vendor::firstOrCreate(
            ['name' => 'PT. Sinar Jaya Dental'],
            [
                'contact_person' => 'Budi Santoso',
                'phone' => '081234567890',
                'address' => 'Jl. Dental Sehat No. 1, Jakarta'
            ]
        );

        Vendor::firstOrCreate(
            ['name' => 'CV. Mitra Medika'],
            [
                'contact_person' => 'Citra Lestari',
                'phone' => '082198765432',
                'address' => 'Jl. Kesehatan Raya No. 45, Surabaya'
            ]
        );

        Vendor::firstOrCreate(
            ['name' => 'Toko Alkes Mandiri'],
            [
                'contact_person' => 'Andi Wijaya',
                'phone' => '085512341234',
                'address' => 'Jl. Merdeka No. 10, Bandung'
            ]
        );
    }
}