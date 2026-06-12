<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StudentsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private int $institutionId;
    private ?int $classId;

    public array $berhasil   = [];
    public array $diperbarui = [];
    public array $gagal      = [];

    /** Cache kelas lembaga: ['nama_kelas_lower' => id] */
    private array $kelasCache = [];

    public function __construct(int $institutionId, ?int $classId = null)
    {
        $this->institutionId = $institutionId;
        $this->classId       = $classId;

        // Build cache kelas untuk lookup nama kelas (case-insensitive)
        SchoolClass::where('institution_id', $institutionId)
            ->aktif()
            ->get()
            ->each(function ($k) {
                $this->kelasCache[mb_strtolower(trim($k->nama_kelas))] = $k->id;
            });
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $i => $row) {
            $baris = $i + 2; // baris 1 = heading

            $nis         = trim((string) ($row['nis'] ?? ''));
            $namaLengkap = trim((string) ($row['nama_lengkap'] ?? ''));
            $jenisKel    = strtoupper(trim((string) ($row['jenis_kelamin_lp'] ?? '')));

            // Validasi kolom wajib
            if (empty($nis) || empty($namaLengkap) || ! in_array($jenisKel, ['L', 'P'])) {
                $this->gagal[] = [
                    'baris'  => $baris,
                    'nis'    => $nis ?: '—',
                    'nama'   => $namaLengkap ?: '—',
                    'alasan' => 'Kolom NIS, Nama Lengkap, atau Jenis Kelamin tidak valid/kosong.',
                ];
                continue;
            }

            // Resolve class_id dari nama kelas di kolom Excel
            $classId = $this->classId; // default dari form selector

            $namaKelasExcel = trim((string) ($row['nama_kelas_lihat_sheet_referensi'] ?? ''));
            if (! empty($namaKelasExcel)) {
                $lookup = mb_strtolower($namaKelasExcel);
                if (isset($this->kelasCache[$lookup])) {
                    $classId = $this->kelasCache[$lookup];
                } else {
                    // Kelas tidak ditemukan — simpan tetap, class_id null
                    $classId = null;
                }
            }

            // Parse tanggal lahir dd/mm/yyyy
            $tanggalLahir = null;
            $tglRaw = trim((string) ($row['tanggal_lahir_ddmmyyyy'] ?? ''));
            if ($tglRaw) {
                try {
                    $tanggalLahir = Carbon::createFromFormat('d/m/Y', $tglRaw)->format('Y-m-d');
                } catch (\Exception) {
                    try { $tanggalLahir = Carbon::parse($tglRaw)->format('Y-m-d'); } catch (\Exception) {}
                }
            }

            $data = [
                'institution_id' => $this->institutionId,
                'class_id'       => $classId,
                'nis'            => $nis,
                'nisn'           => trim((string) ($row['nisn'] ?? '')) ?: null,
                'nama_lengkap'   => $namaLengkap,
                'jenis_kelamin'  => $jenisKel,
                'tanggal_lahir'  => $tanggalLahir,
                'tempat_lahir'   => trim((string) ($row['tempat_lahir'] ?? '')) ?: null,
                'alamat'         => trim((string) ($row['alamat'] ?? '')) ?: null,
                'no_hp_ortu'     => trim((string) ($row['no_hp_orang_tua'] ?? '')) ?: null,
                'nama_ortu'      => trim((string) ($row['nama_orang_tuawali'] ?? '')) ?: null,
                'aktif'          => true,
            ];

            // Cek duplikat NIS
            $existing = Student::withTrashed()
                ->where('institution_id', $this->institutionId)
                ->where('nis', $nis)
                ->first();

            if ($existing && ! $existing->trashed()) {
                $existing->update($data);
                $this->diperbarui[] = ['baris' => $baris, 'nis' => $nis, 'nama' => $namaLengkap];
            } elseif ($existing && $existing->trashed()) {
                $existing->restore();
                $existing->update($data);
                $this->berhasil[] = ['baris' => $baris, 'nis' => $nis, 'nama' => $namaLengkap];
            } else {
                Student::create($data);
                $this->berhasil[] = ['baris' => $baris, 'nis' => $nis, 'nama' => $namaLengkap];
            }
        }
    }
}
