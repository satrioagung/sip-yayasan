<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'institution_id',
        'bill_id',
        'student_id',
        'petugas_id',
        'nomor_transaksi',
        'tanggal_bayar',
        'metode_bayar',
        'nominal_bayar',
        'bukti_file',
        'keterangan',
    ];

    protected $casts = [
        'nominal_bayar' => 'integer',
        'tanggal_bayar' => 'date',
    ];

    public static array $metodeBayarLabels = [
        'tunai'        => 'Tunai',
        'transfer'     => 'Transfer Bank',
        'qris'         => 'QRIS',
        'kartu_debit'  => 'Kartu Debit',
        'kartu_kredit' => 'Kartu Kredit',
        'lainnya'      => 'Lainnya',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function getNominalBayarFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_bayar, 0, ',', '.');
    }

    public function getMetodeBayarLabelAttribute(): string
    {
        return self::$metodeBayarLabels[$this->metode_bayar] ?? $this->metode_bayar;
    }

    public function getBuktiUrlAttribute(): ?string
    {
        if ($this->bukti_file && Storage::disk('public')->exists($this->bukti_file)) {
            return Storage::url($this->bukti_file);
        }
        return null;
    }

    public function getTanggalBayarFormatAttribute(): string
    {
        return $this->tanggal_bayar?->translatedFormat('d F Y') ?? '-';
    }

    public function scopeForInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }
}
