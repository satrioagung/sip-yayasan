<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private int $institutionId,
        private ?int $classId = null,
    ) {}

    public function title(): string
    {
        return 'Data Siswa';
    }

    public function query()
    {
        return Student::with('class')
            ->forInstitution($this->institutionId)
            ->when($this->classId, fn ($q) => $q->where('class_id', $this->classId))
            ->orderBy('nama_lengkap');
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'NISN',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Kelas',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'No. HP Orang Tua',
            'Nama Orang Tua/Wali',
            'Status',
        ];
    }

    public function map($student): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $student->nis,
            $student->nisn ?? '',
            $student->nama_lengkap,
            $student->jenis_kelamin_label,
            $student->class?->nama_kelas ?? '-',
            $student->tempat_lahir ?? '',
            $student->tanggal_lahir?->format('d/m/Y') ?? '',
            $student->alamat ?? '',
            $student->no_hp_ortu ?? '',
            $student->nama_ortu ?? '',
            $student->aktif ? 'Aktif' : 'Nonaktif',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Baris header
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
