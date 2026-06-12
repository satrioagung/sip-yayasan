<?php

namespace App\Exports;

use App\Models\Institution;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentsTemplateReferensiSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    private Collection $kelas;
    private bool $isSuperAdmin;
    private int $institutionId;

    public function __construct(Collection $kelas, bool $isSuperAdmin, int $institutionId)
    {
        $this->kelas         = $kelas;
        $this->isSuperAdmin  = $isSuperAdmin;
        $this->institutionId = $institutionId;
    }

    public function title(): string
    {
        return 'Referensi Kelas';
    }

    public function collection(): Collection
    {
        $rows = collect();

        foreach ($this->kelas as $k) {
            $rows->push([
                $k->nama_kelas,
                $k->tingkat,
                $k->jurusan ?? '—',
                $k->schoolYear?->nama ?? '—',
                $k->wali_kelas ?? '—',
                $k->aktif ? 'Aktif' : 'Nonaktif',
            ]);
        }

        if ($rows->isEmpty()) {
            $rows->push(['(Belum ada kelas)', '', '', '', '', '']);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Nama Kelas ✓ (gunakan tepat seperti ini)',
            'Tingkat',
            'Jurusan',
            'Tahun Ajaran',
            'Wali Kelas',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header biru gelap
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
        ]);

        // Kolom Nama Kelas highlight kuning
        $lastRow = $this->kelas->count() + 1;
        if ($lastRow > 1) {
            $sheet->getStyle("A2:A{$lastRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '1E40AF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            ]);
        }

        // Panduan di baris setelah data
        $noteRow = $lastRow + 2;
        $sheet->setCellValue("A{$noteRow}", 'Petunjuk: Salin tepat Nama Kelas dari kolom A di atas ke Sheet "Template Import" kolom E.');
        $sheet->getStyle("A{$noteRow}")->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280'], 'size' => 9],
        ]);
        $sheet->mergeCells("A{$noteRow}:F{$noteRow}");

        return [];
    }
}
