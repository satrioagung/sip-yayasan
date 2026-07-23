<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

/**
 * Dry-run import — baca Excel dan validasi tanpa menyimpan ke DB.
 * Digunakan untuk menampilkan preview sebelum import sesungguhnya.
 */
class StudentsPreviewImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private int $institutionId;
    private ?int $classId;
    private array $kelasCache = [];

    public int   $total   = 0;
    public int   $valid   = 0;
    public int   $invalid = 0;
    public array $rows    = [];

    public function __construct(int $institutionId, ?int $classId = null)
    {
        $this->institutionId = $institutionId;
        $this->classId       = $classId;

        SchoolClass::where('institution_id', $institutionId)
            ->aktif()->get()
            ->each(function ($k) {
                $this->kelasCache[mb_strtolower(trim($k->nama_kelas))] = $k->nama_kelas;
            });
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $i => $row) {
            $baris       = $i + 2;
            $nis         = trim((string) ($row['nis'] ?? ''));
            $namaLengkap = trim((string) ($row['nama_lengkap'] ?? ''));
            $jenisKel    = strtoupper(trim((string) ($row['jenis_kelamin_lp'] ?? '')));

            $this->total++;

            // Cek validitas kolom wajib
            $isValid = ! empty($nis) && ! empty($namaLengkap) && in_array($jenisKel, ['L', 'P']);

            // Tentukan kelas
            $kelasNama = null;
            $namaKelasExcel = trim((string) ($row['nama_kelas_lihat_sheet_referensi'] ?? ''));
            if (! empty($namaKelasExcel)) {
                $lookup = mb_strtolower($namaKelasExcel);
                $kelasNama = $this->kelasCache[$lookup] ?? $namaKelasExcel . ' (tidak ditemukan)';
            }

            // Cek apakah NIS sudah ada (untuk label Baru/Update)
            $statusSiswa = 'baru';
            if ($isValid) {
                $existing = Student::withTrashed()
                    ->where('institution_id', $this->institutionId)
                    ->where('nis', $nis)
                    ->whereNull('deleted_at')
                    ->first();
                if ($existing) {
                    $statusSiswa = 'update';
                }
                $this->valid++;
            } else {
                $this->invalid++;
            }

            // Keterangan error
            $errorMsg = null;
            if (! $isValid) {
                $errors = [];
                if (empty($nis))         $errors[] = 'NIS kosong';
                if (empty($namaLengkap)) $errors[] = 'Nama kosong';
                if (! in_array($jenisKel, ['L', 'P'])) $errors[] = 'JK tidak valid';
                $errorMsg = implode(', ', $errors);
            }

            $this->rows[] = [
                'baris'       => $baris,
                'nis'         => $nis ?: '—',
                'nama'        => $namaLengkap ?: '—',
                'jk'          => $jenisKel ?: '—',
                'kelas'       => $kelasNama ?? '—',
                'status'      => $statusSiswa,
                'valid'       => $isValid,
                'error'       => $errorMsg,
            ];
        }
    }
}
