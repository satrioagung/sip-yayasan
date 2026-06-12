<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'students';

    protected $fillable = [
        'institution_id',
        'class_id',
        'nis',
        'nisn',
        'nama_lengkap',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'alamat',
        'no_hp_ortu',
        'nama_ortu',
        'foto',
        'aktif',
    ];

    protected $casts = [
        'aktif'         => 'boolean',
        'tanggal_lahir' => 'date',
    ];

    /** Relasi ke institusi */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** Relasi ke kelas */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /** URL foto siswa */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return Storage::url($this->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_lengkap) . '&background=2563eb&color=fff&size=100';
    }

    /** Label jenis kelamin */
    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /** Format tanggal lahir Indonesia */
    public function getTanggalLahirFormatAttribute(): ?string
    {
        return $this->tanggal_lahir?->translatedFormat('d F Y');
    }

    /** Scope hanya siswa aktif */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /** Scope berdasarkan institusi */
    public function scopeForInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }
}
