<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_types';

    protected $fillable = [
        'institution_id',
        'school_year_id',
        'nama',
        'kode',
        'nominal_default',
        'tipe',
        'bisa_cicil',
        'wajib',
        'aktif',
        'keterangan',
    ];

    protected $casts = [
        'nominal_default' => 'integer',
        'bisa_cicil'      => 'boolean',
        'wajib'           => 'boolean',
        'aktif'           => 'boolean',
    ];

    /** Label tipe pembayaran */
    public static array $tipeLabels = [
        'bulanan' => 'Bulanan',
        'tahunan' => 'Tahunan',
        'sekali'  => 'Sekali Bayar',
        'bebas'   => 'Bebas',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /** Format nominal default ke Rupiah */
    public function getNominalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_default, 0, ',', '.');
    }

    /** Label tipe */
    public function getTipeLabelAttribute(): string
    {
        return self::$tipeLabels[$this->tipe] ?? $this->tipe;
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeForInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }
}
