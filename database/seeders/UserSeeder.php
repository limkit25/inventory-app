<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // <-- Impor model User
use Illuminate\Support\Facades\Hash; // <-- Impor Hash
use Spatie\Permission\Models\Role; // <-- Impor Role
use Spatie\Permission\Models\Permission; // <-- Impor Permission
use Illuminate\Support\Facades\DB; // <-- Impor DB Facade

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bungkus dalam transaksi untuk kecepatan dan keamanan
        DB::transaction(function () {
            
            // 1. Buat Permissions (Izin)
            $permissions = [
                'manage-users',
                'manage-master-data',
                'perform-transactions',
                'view-reports',
                'approve-adjustments' // Termasuk izin approval
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            // 2. Buat Roles (Peran)
            $adminRole = Role::firstOrCreate(['name' => 'Admin']);
            $gudangRole = Role::firstOrCreate(['name' => 'Gudang']);

            // 3. Beri Izin ke Role
            $adminRole->givePermissionTo(Permission::all()); // Admin bisa semua
            $gudangRole->givePermissionTo(['perform-transactions', 'view-reports']);

            
            // 4. Buat Pengguna ADMIN
            $adminUser = User::firstOrCreate(
                ['email' => 'admin@email.com'], // Cari berdasarkan email
                [
                    'name' => 'Admin User',
                    'password' => Hash::make('password') // Passwordnya 'password'
                ]
            );
            // Berikan role Admin
            $adminUser->assignRole($adminRole);


            // 5. Buat Pengguna GUDANG
            $gudangUser = User::firstOrCreate(
                ['email' => 'gudang@email.com'], // Cari berdasarkan email
                [
                    'name' => 'Staff Gudang',
                    'password' => Hash::make('password') // Passwordnya 'password'
                ]
            );
            // Berikan role Gudang
            $gudangUser->assignRole($gudangRole);
        });
    }
}