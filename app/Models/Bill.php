<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bills';

    protected $fillable = [
        'institution_id',
        'student_id',
        'payment_type_id',
        'school_year_id',
        'bulan',
        'tahun',
        'nominal',
        'nominal_terbayar',
        'status',
        'jatuh_tempo',
        'keterangan',
    ];

    protected $casts = [
        'nominal'          => 'integer',
        'nominal_terbayar' => 'integer',
        'bulan'            => 'integer',
        'tahun'            => 'integer',
        'jatuh_tempo'      => 'date',
    ];

    /** Label status dengan warna Tailwind */
    public static array $statusConfig = [
        'belum_bayar' => ['label' => 'Belum Bayar', 'bg' => 'bg-red-100',    'text' => 'text-red-700',    'dot' => 'bg-red-500'],
        'sebagian'    => ['label' => 'Sebagian',    'bg' => 'bg-amber-100',  'text' => 'text-amber-700',  'dot' => 'bg-amber-500'],
        'lunas'       => ['label' => 'Lunas',       'bg' => 'bg-green-100',  'text' => 'text-green-700',  'dot' => 'bg-green-500'],
    ];

    /** Label bulan Indonesia */
    public static array $bulanLabels = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April',   5 => 'Mei',      6 => 'Juni',
        7 => 'Juli',    8 => 'Agustus',  9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /** Sisa tagihan */
    public function getSisaAttribute(): int
    {
        return $this->nominal - $this->nominal_terbayar;
    }

    /** Format nominal */
    public function getNominalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getNominalTerbayarFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_terbayar, 0, ',', '.');
    }

    public function getSisaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->sisa, 0, ',', '.');
    }

    /** Label bulan */
    public function getBulanLabelAttribute(): string
    {
        return $this->bulan ? (self::$bulanLabels[$this->bulan] ?? '-') : '-';
    }

    /** Periode tagihan */
    public function getPeriodeAttribute(): string
    {
        if ($this->bulan) {
            return ($this->bulan_label ?? '') . ' ' . $this->tahun;
        }
        return (string) $this->tahun;
    }

    /** Status config */
    public function getStatusConfigAttribute(): array
    {
        return self::$statusConfig[$this->status] ?? self::$statusConfig['belum_bayar'];
    }

    public function scopeForInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
