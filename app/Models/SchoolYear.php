<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'school_years';

    protected $fillable = [
        'institution_id',
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif',
    ];

    protected $casts = [
        'aktif'          => 'boolean',
        'tanggal_mulai'  => 'date',
        'tanggal_selesai'=> 'date',
    ];

    /** Relasi ke institusi */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** Relasi ke kelas */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'school_year_id');
    }

    /** Set tahun ajaran ini aktif, nonaktifkan yang lain di institusi yang sama */
    public function setAsAktif(): void
    {
        // Nonaktifkan semua tahun ajaran lain di institusi yang sama
        static::where('institution_id', $this->institution_id)
            ->where('id', '!=', $this->id)
            ->update(['aktif' => false]);

        $this->update(['aktif' => true]);
    }

    /** Scope hanya yang aktif */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /** Format label nama tahun ajaran */
    public function getLabelAttribute(): string
    {
        $status = $this->aktif ? ' (Aktif)' : '';
        return $this->nama . $status;
    }
}
