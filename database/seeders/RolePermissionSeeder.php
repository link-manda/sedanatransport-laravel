<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Permissions (jika diperlukan, untuk sekarang kita pakai roles saja)
        // Permission::create(['name' => 'manage vehicles']);

        // Buat Roles
        $adminRole = Role::create(['name' => 'admin']);
        $petugasRole = Role::create(['name' => 'petugas']);
        $pelangganRole = Role::create(['name' => 'pelanggan']);

        // $adminRole->givePermissionTo('manage vehicles');
        // $petugasRole->givePermissionTo('manage vehicles');

        // Buat User Admin
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@rental.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole($adminRole);

        // Buat User Petugas (Contoh)
        $petugasUser = User::factory()->create([
            'name' => 'Petugas User',
            'email' => 'petugas@rental.com',
            'password' => bcrypt('password')
        ]);
        $petugasUser->assignRole($petugasRole);

        // Buat User Pelanggan (Contoh)
        $pelangganUser = User::factory()->create([
            'name' => 'Pelanggan User',
            'email' => 'pelanggan@rental.com',
            'password' => bcrypt('password')
        ]);
        $pelangganUser->assignRole($pelangganRole);
    }
}
