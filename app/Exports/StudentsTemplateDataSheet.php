<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentsTemplateDataSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private int $institutionId) {}

    public function title(): string
    {
        return 'Template Import';
    }

    public function array(): array
    {
        // Contoh 2 baris data panduan pengisian
        return [
            [
                '2024001',          // NIS
                '0012345678',       // NISN
                'Budi Santoso',     // Nama Lengkap
                'L',                // Jenis Kelamin
                'X RPL 1',          // Nama Kelas (harus sesuai Sheet 2)
                '07/01/2009',       // Tanggal Lahir
                'Jakarta',          // Tempat Lahir
                'Jl. Merdeka No. 1', // Alamat
                '08123456789',      // No. HP Orang Tua
                'Siti Rahayu',      // Nama Orang Tua/Wali
            ],
            [
                '2024002',
                '0012345679',
                'Sari Dewi',
                'P',
                'X RPL 2',
                '15/03/2009',
                'Bandung',
                'Jl. Pahlawan No. 5',
                '08987654321',
                'Ahmad Fauzi',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'NIS *',
            'NISN',
            'Nama Lengkap *',
            'Jenis Kelamin (L/P) *',
            'Nama Kelas (lihat Sheet Referensi)',
            'Tanggal Lahir (dd/mm/yyyy)',
            'Tempat Lahir',
            'Alamat',
            'No. HP Orang Tua',
            'Nama Orang Tua/Wali',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header biru
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
        ]);

        // Kolom wajib: NIS (A), Nama Lengkap (C), Jenis Kelamin (D) — tint kuning
        foreach (['A', 'C', 'D'] as $col) {
            $sheet->getStyle("{$col}1")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            ]);
        }

        // Kolom Nama Kelas (E) — tint hijau gelap sebagai petunjuk referensi
        $sheet->getStyle('E1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '15803D']],
        ]);

        // Baris data contoh: warna abu muda
        $sheet->getStyle('A2:J3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            'font' => ['color' => ['rgb' => '64748B']],
        ]);

        // Catatan kaki di baris 5
        $sheet->setCellValue('A5', '* Kolom bertanda * wajib diisi. Nama Kelas harus sesuai persis dengan yang ada di Sheet "Referensi Kelas".');
        $sheet->getStyle('A5')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => 'EF4444'], 'size' => 9],
        ]);
        $sheet->mergeCells('A5:J5');

        return [];
    }
}
