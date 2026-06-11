<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Institution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'jenjang',
        'code',
        'address',
        'phone',
        'email',
        'principal_name',
        'nip_kepala',
        'logo',
        'warna_tema',
        'footer_struk',
        'prefix_nomor_struk',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Daftar jenjang yang tersedia.
     */
    public static function daftarJenjang(): array
    {
        return ['TK', 'SD', 'MI', 'SMP', 'MTs', 'SMA', 'MA', 'SMK', 'Pesantren', 'Lainnya'];
    }

    /**
     * URL logo lembaga.
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return Storage::url($this->logo);
        }
        return asset('images/default-institution.png');
    }

    /**
     * Nama lengkap lembaga dengan jenjang.
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->jenjang
            ? "{$this->jenjang} {$this->name}"
            : $this->name;
    }

    /**
     * Status aktif dalam teks Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    /**
     * Relasi ke pengguna dalam institusi ini.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope hanya lembaga aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
