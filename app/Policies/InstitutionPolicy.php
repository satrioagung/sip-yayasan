<?php

namespace App\Policies;

use App\Models\Institution;
use App\Models\User;

class InstitutionPolicy
{
    /**
     * Super Admin bisa melakukan apapun.
     * Berlaku sebelum semua method lain.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        return null;
    }

    /**
     * Lihat daftar lembaga — hanya Super Admin (sudah ditangani before()).
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Lihat detail lembaga — Super Admin atau user dari lembaga tersebut.
     */
    public function view(User $user, Institution $institution): bool
    {
        return (int) $user->institution_id === (int) $institution->id;
    }

    /**
     * Buat lembaga baru — hanya Super Admin.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Edit lembaga — hanya Super Admin.
     */
    public function update(User $user, Institution $institution): bool
    {
        return false;
    }

    /**
     * Hapus lembaga — hanya Super Admin.
     */
    public function delete(User $user, Institution $institution): bool
    {
        return false;
    }

    /**
     * Toggle aktif/nonaktif — hanya Super Admin.
     */
    public function toggleActive(User $user, Institution $institution): bool
    {
        return false;
    }

    /**
     * Restore lembaga yang dihapus.
     */
    public function restore(User $user, Institution $institution): bool
    {
        return false;
    }

    /**
     * Hapus permanen lembaga.
     */
    public function forceDelete(User $user, Institution $institution): bool
    {
        return false;
    }
}
