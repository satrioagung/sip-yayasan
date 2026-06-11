<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat institusi demo
        $institution = Institution::firstOrCreate(
            ['code' => 'SMK-DEMO'],
            [
                'name'           => 'SMK Yayasan Demo',
                'address'        => 'Jl. Pendidikan No. 1, Jakarta',
                'phone'          => '021-12345678',
                'email'          => 'info@smk-demo.sch.id',
                'principal_name' => 'Drs. Kepala Sekolah, M.Pd.',
                'is_active'      => true,
            ]
        );

        // ============================================
        // 1. Super Admin (tidak perlu institution_id)
        // ============================================
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@sip-spp.id'],
            [
                'name'           => 'Super Administrator',
                'password'       => Hash::make('password'),
                'role'           => 'super_admin',
                'institution_id' => null,
                'is_active'      => true,
            ]
        );
        $superAdmin->syncRoles(['Super Admin']);

        // ============================================
        // 2. Admin Sekolah
        // ============================================
        $adminSekolah = User::firstOrCreate(
            ['email' => 'admin@sip-spp.id'],
            [
                'name'           => 'Admin Sekolah',
                'password'       => Hash::make('password'),
                'role'           => 'admin_sekolah',
                'institution_id' => $institution->id,
                'is_active'      => true,
            ]
        );
        $adminSekolah->syncRoles(['Admin Sekolah']);

        // ============================================
        // 3. Bendahara
        // ============================================
        $bendahara = User::firstOrCreate(
            ['email' => 'bendahara@sip-spp.id'],
            [
                'name'           => 'Bendahara Sekolah',
                'password'       => Hash::make('password'),
                'role'           => 'bendahara',
                'institution_id' => $institution->id,
                'is_active'      => true,
            ]
        );
        $bendahara->syncRoles(['Bendahara']);

        // ============================================
        // 4. Siswa
        // ============================================
        $siswa = User::firstOrCreate(
            ['email' => 'siswa@sip-spp.id'],
            [
                'name'           => 'Siswa Demo',
                'password'       => Hash::make('password'),
                'role'           => 'siswa',
                'nis'            => '2024001',
                'institution_id' => $institution->id,
                'is_active'      => true,
            ]
        );
        $siswa->syncRoles(['Siswa']);

        $this->command->info('✅ Akun pengguna berhasil dibuat:');
        $this->command->table(
            ['Nama', 'Email', 'Role', 'Password'],
            [
                ['Super Administrator', 'superadmin@sip-spp.id', 'Super Admin', 'password'],
                ['Admin Sekolah',       'admin@sip-spp.id',       'Admin Sekolah', 'password'],
                ['Bendahara Sekolah',   'bendahara@sip-spp.id',   'Bendahara',    'password'],
                ['Siswa Demo',          'siswa@sip-spp.id',        'Siswa',        'password'],
            ]
        );
    }
}
