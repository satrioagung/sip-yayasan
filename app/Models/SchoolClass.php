<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'institution_id',
        'school_year_id',
        'nama_kelas',
        'tingkat',
        'jurusan',
        'wali_kelas',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /** Relasi ke institusi */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** Relasi ke tahun ajaran */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id');
    }

    /** Relasi ke siswa */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    /** Label lengkap: Tingkat + Nama Kelas */
    public function getNamaLengkapAttribute(): string
    {
        return $this->nama_kelas;
    }

    /** Scope hanya kelas aktif */
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
