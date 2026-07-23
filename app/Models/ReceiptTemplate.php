<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReceiptTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'receipt_templates';

    protected $fillable = [
        'institution_id',
        'nama_template',
        'header',
        'footer',
        'show_logo',
        'show_qr',
        'ukuran',
        'is_default',
    ];

    protected $casts = [
        'show_logo'  => 'boolean',
        'show_qr'    => 'boolean',
        'is_default' => 'boolean',
    ];

    public static array $ukuranLabels = [
        'a4'        => 'A4 (210 × 297 mm)',
        'thermal58' => 'Thermal 58mm',
        'thermal80' => 'Thermal 80mm',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** Ganti semua template lain menjadi non-default, kemudian set ini sebagai default */
    public function setAsDefault(): void
    {
        static::where('institution_id', $this->institution_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }

    public function getUkuranLabelAttribute(): string
    {
        return self::$ukuranLabels[$this->ukuran] ?? $this->ukuran;
    }

    public function scopeForInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }
}
