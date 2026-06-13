<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\PaymentType;
use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BillGeneratorService
{
    /**
     * Preview siswa yang akan ditagih.
     * Return: collection of students, sudah_ada (count duplikat), akan_dibuat (count baru)
     */
    public function preview(array $params): array
    {
        $students = $this->queryStudents($params);
        $paymentType = PaymentType::findOrFail($params['payment_type_id']);

        $bulan = $params['bulan'] ?? null;
        $tahun = (int) $params['tahun'];

        // Cek yang sudah ada
        $existingStudentIds = Bill::withTrashed()
            ->whereIn('student_id', $students->pluck('id'))
            ->where('payment_type_id', $paymentType->id)
            ->where('tahun', $tahun)
            ->when($bulan, fn($q) => $q->where('bulan', $bulan))
            ->pluck('student_id')
            ->toArray();

        $sudahAda    = count($existingStudentIds);
        $akanDibuat  = $students->count() - $sudahAda;

        return [
            'students'         => $students,
            'payment_type'     => $paymentType,
            'sudah_ada'        => $sudahAda,
            'akan_dibuat'      => $akanDibuat,
            'existing_ids'     => $existingStudentIds,
        ];
    }

    /**
     * Generate tagihan secara massal.
     * Return: ['berhasil' => int, 'dilewati' => int]
     */
    public function generate(array $params): array
    {
        $students    = $this->queryStudents($params);
        $paymentType = PaymentType::findOrFail($params['payment_type_id']);

        $bulan      = isset($params['bulan']) && $params['bulan'] !== '' ? (int) $params['bulan'] : null;
        $tahun      = (int) $params['tahun'];
        $nominal    = (int) ($params['nominal'] ?? $paymentType->nominal_default);
        $jatuhTempo = isset($params['jatuh_tempo']) && $params['jatuh_tempo'] !== ''
            ? Carbon::createFromFormat('d/m/Y', $params['jatuh_tempo'])
            : null;

        $berhasil = 0;
        $dilewati = 0;

        foreach ($students as $student) {
            // Cek duplikat
            $exists = Bill::withTrashed()
                ->where('student_id', $student->id)
                ->where('payment_type_id', $paymentType->id)
                ->where('tahun', $tahun)
                ->when($bulan !== null, fn($q) => $q->where('bulan', $bulan))
                ->exists();

            if ($exists) {
                $dilewati++;
                continue;
            }

            Bill::create([
                'institution_id'   => $student->institution_id,
                'student_id'       => $student->id,
                'payment_type_id'  => $paymentType->id,
                'school_year_id'   => $params['school_year_id'] ?? null,
                'bulan'            => $bulan,
                'tahun'            => $tahun,
                'nominal'          => $nominal,
                'nominal_terbayar' => 0,
                'status'           => 'belum_bayar',
                'jatuh_tempo'      => $jatuhTempo,
                'keterangan'       => $params['keterangan'] ?? null,
            ]);

            $berhasil++;
        }

        return ['berhasil' => $berhasil, 'dilewati' => $dilewati];
    }

    /** Query siswa berdasarkan scope: semua / per kelas / per siswa */
    private function queryStudents(array $params): Collection
    {
        $institutionId = (int) $params['institution_id'];

        $query = Student::with('class')
            ->forInstitution($institutionId)
            ->aktif();

        if (! empty($params['class_id'])) {
            $query->where('class_id', $params['class_id']);
        }

        if (! empty($params['student_id'])) {
            $query->where('id', $params['student_id']);
        }

        return $query->orderBy('nama_lengkap')->get();
    }
}
