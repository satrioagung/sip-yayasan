<?php

namespace App\Exports;

use App\Models\Institution;
use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentsTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private int $institutionId,
        private bool $isSuperAdmin = false,
    ) {}

    public function sheets(): array
    {
        $kelas = SchoolClass::where('institution_id', $this->institutionId)
            ->aktif()
            ->with('schoolYear')
            ->orderBy('nama_kelas')
            ->get();

        $sheets = [
            new StudentsTemplateDataSheet($this->institutionId),
            new StudentsTemplateReferensiSheet($kelas, $this->isSuperAdmin, $this->institutionId),
        ];

        return $sheets;
    }
}
