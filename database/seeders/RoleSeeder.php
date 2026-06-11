<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Definisi permissions per modul
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Manajemen Pengguna
            'pengguna.view', 'pengguna.create', 'pengguna.edit', 'pengguna.delete',

            // Manajemen Institusi
            'institusi.view', 'institusi.create', 'institusi.edit', 'institusi.delete',

            // Manajemen Siswa
            'siswa.view', 'siswa.create', 'siswa.edit', 'siswa.delete',

            // Manajemen Kelas
            'kelas.view', 'kelas.create', 'kelas.edit', 'kelas.delete',

            // Tagihan SPP
            'tagihan.view', 'tagihan.create', 'tagihan.edit', 'tagihan.delete',

            // Pembayaran SPP
            'pembayaran.view', 'pembayaran.create', 'pembayaran.edit', 'pembayaran.delete',
            'pembayaran.approve', 'pembayaran.cetak',

            // Laporan
            'laporan.view', 'laporan.export',

            // Pengaturan
            'pengaturan.view', 'pengaturan.edit',
        ];

        // Buat semua permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // =============================================
        // ROLE: Super Admin — akses penuh semua fitur
        // =============================================
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // =============================================
        // ROLE: Admin Sekolah — kelola data sekolahnya
        // =============================================
        $adminSekolah = Role::firstOrCreate(['name' => 'Admin Sekolah', 'guard_name' => 'web']);
        $adminSekolah->syncPermissions([
            'dashboard.view',
            'pengguna.view', 'pengguna.create', 'pengguna.edit',
            'siswa.view', 'siswa.create', 'siswa.edit', 'siswa.delete',
            'kelas.view', 'kelas.create', 'kelas.edit', 'kelas.delete',
            'tagihan.view', 'tagihan.create', 'tagihan.edit', 'tagihan.delete',
            'pembayaran.view', 'pembayaran.approve', 'pembayaran.cetak',
            'laporan.view', 'laporan.export',
            'pengaturan.view', 'pengaturan.edit',
        ]);

        // =============================================
        // ROLE: Bendahara — kelola keuangan
        // =============================================
        $bendahara = Role::firstOrCreate(['name' => 'Bendahara', 'guard_name' => 'web']);
        $bendahara->syncPermissions([
            'dashboard.view',
            'siswa.view',
            'tagihan.view', 'tagihan.create', 'tagihan.edit',
            'pembayaran.view', 'pembayaran.create', 'pembayaran.edit',
            'pembayaran.approve', 'pembayaran.cetak',
            'laporan.view', 'laporan.export',
        ]);

        // =============================================
        // ROLE: Siswa — hanya lihat tagihan & riwayat
        // =============================================
        $siswa = Role::firstOrCreate(['name' => 'Siswa', 'guard_name' => 'web']);
        $siswa->syncPermissions([
            'dashboard.view',
            'tagihan.view',
            'pembayaran.view', 'pembayaran.cetak',
        ]);

        $this->command->info('✅ Role dan Permission berhasil dibuat.');
    }
}
