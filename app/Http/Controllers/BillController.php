<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\PaymentType;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Services\BillGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillController extends Controller
{
    public function __construct(private BillGeneratorService $generator) {}

    private function institutionId(Request $request): int
    {
        $user = $request->user();
        if ($user->hasRole('Super Admin')) {
            return (int) ($request->session()->get('active_tenant_id') ?? abort(403, 'Pilih lembaga aktif terlebih dahulu.'));
        }
        return (int) $user->institution_id;
    }

    /** Daftar tagihan dengan filter */
    public function index(Request $request): View
    {
        $institutionId = $this->institutionId($request);

        $bills = Bill::with(['student.class', 'paymentType', 'schoolYear'])
            ->forInstitution($institutionId)
            ->when($request->search, function ($q) use ($request) {
                $s = $request->search;
                $q->whereHas('student', fn($sq) =>
                    $sq->where('nama_lengkap', 'ilike', "%{$s}%")
                       ->orWhere('nis', 'ilike', "%{$s}%")
                );
            })
            ->when($request->payment_type_id, fn($q) => $q->where('payment_type_id', $request->payment_type_id))
            ->when($request->school_year_id,  fn($q) => $q->where('school_year_id',  $request->school_year_id))
            ->when($request->class_id, fn($q) => $q->whereHas('student', fn($sq) => $sq->where('class_id', $request->class_id)))
            ->when($request->status,   fn($q) => $q->where('status', $request->status))
            ->when($request->bulan,    fn($q) => $q->where('bulan', $request->bulan))
            ->when($request->tahun,    fn($q) => $q->where('tahun', $request->tahun))
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->orderBy(Student::select('nama_lengkap')->whereColumn('students.id', 'bills.student_id')->limit(1))
            ->paginate(25)
            ->withQueryString();

        // Data untuk filter
        $paymentTypes = PaymentType::forInstitution($institutionId)->aktif()->orderBy('nama')->get();
        $schoolYears  = SchoolYear::where('institution_id', $institutionId)->orderByDesc('tanggal_mulai')->get();
        $kelas        = SchoolClass::where('institution_id', $institutionId)->aktif()->orderBy('nama_kelas')->get();

        // Statistik cepat
        $stats = [
            'total'       => Bill::forInstitution($institutionId)->count(),
            'belum_bayar' => Bill::forInstitution($institutionId)->where('status', 'belum_bayar')->count(),
            'sebagian'    => Bill::forInstitution($institutionId)->where('status', 'sebagian')->count(),
            'lunas'       => Bill::forInstitution($institutionId)->where('status', 'lunas')->count(),
            'total_nominal'    => Bill::forInstitution($institutionId)->sum('nominal'),
            'total_terbayar'   => Bill::forInstitution($institutionId)->sum('nominal_terbayar'),
        ];

        return view('bills.index', compact('bills', 'paymentTypes', 'schoolYears', 'kelas', 'stats'));
    }

    /** Form generate tagihan */
    public function generateForm(Request $request): View
    {
        $institutionId = $this->institutionId($request);

        $paymentTypes = PaymentType::forInstitution($institutionId)->aktif()->orderBy('nama')->get();
        $schoolYears  = SchoolYear::where('institution_id', $institutionId)->orderByDesc('tanggal_mulai')->get();
        $kelas        = SchoolClass::where('institution_id', $institutionId)->aktif()->orderBy('nama_kelas')->get();
        $students     = Student::forInstitution($institutionId)->aktif()->orderBy('nama_lengkap')->with('class')->get();

        return view('bills.generate', compact('paymentTypes', 'schoolYears', 'kelas', 'students'));
    }

    /** Preview AJAX: hitung siswa yang akan ditagih */
    public function preview(Request $request): JsonResponse
    {
        $institutionId = $this->institutionId($request);

        $request->validate([
            'payment_type_id' => ['required', 'exists:payment_types,id'],
            'tahun'           => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $params = array_merge($request->only(['payment_type_id', 'bulan', 'tahun', 'class_id', 'student_id']), [
            'institution_id' => $institutionId,
        ]);

        $result = $this->generator->preview($params);

        return response()->json([
            'akan_dibuat' => $result['akan_dibuat'],
            'sudah_ada'   => $result['sudah_ada'],
            'total'       => $result['students']->count(),
            'siswa'       => $result['students']->take(10)->map(fn($s) => [
                'nama'       => $s->nama_lengkap,
                'nis'        => $s->nis,
                'kelas'      => $s->class?->nama_kelas ?? '—',
                'is_dup'     => in_array($s->id, $result['existing_ids']),
            ])->values(),
        ]);
    }

    /** Generate tagihan */
    public function generateStore(Request $request): RedirectResponse
    {
        $institutionId = $this->institutionId($request);

        $request->validate([
            'payment_type_id' => ['required', 'exists:payment_types,id'],
            'school_year_id'  => ['nullable', 'exists:school_years,id'],
            'tahun'           => ['required', 'integer', 'min:2000', 'max:2100'],
            'bulan'           => ['nullable', 'integer', 'min:1', 'max:12'],
            'nominal'         => ['required', 'integer', 'min:1'],
            'jatuh_tempo'     => ['nullable', 'date'],
            'keterangan'      => ['nullable', 'string', 'max:500'],
            'scope'           => ['required', 'in:semua,kelas,siswa'],
            'class_id'        => ['nullable', 'exists:classes,id'],
            'student_id'      => ['nullable', 'exists:students,id'],
        ], [
            'payment_type_id.required' => 'Jenis pembayaran wajib dipilih.',
            'tahun.required'           => 'Tahun wajib diisi.',
            'nominal.required'         => 'Nominal wajib diisi.',
            'nominal.min'              => 'Nominal harus lebih dari 0.',
            'scope.required'           => 'Pilih cakupan generate tagihan.',
        ]);

        $params = array_merge($request->only([
            'payment_type_id', 'school_year_id', 'tahun', 'bulan',
            'nominal', 'jatuh_tempo', 'keterangan',
        ]), [
            'institution_id' => $institutionId,
            'class_id'       => $request->scope === 'kelas'  ? $request->class_id   : null,
            'student_id'     => $request->scope === 'siswa'  ? $request->student_id : null,
        ]);

        $result = $this->generator->generate($params);

        $pesan = "Tagihan berhasil dibuat: {$result['berhasil']} tagihan baru";
        if ($result['dilewati'] > 0) {
            $pesan .= ", {$result['dilewati']} dilewati (sudah ada).";
        } else {
            $pesan .= '.';
        }

        return redirect()->route('bills.index')
            ->with('success', $pesan);
    }

    /** Hapus satu tagihan */
    public function destroy(Bill $bill, Request $request): RedirectResponse
    {
        $this->authorizeBill($bill, $request);

        if ($bill->nominal_terbayar > 0) {
            return back()->with('error', 'Tagihan yang sudah memiliki pembayaran tidak dapat dihapus.');
        }

        $bill->delete();

        return back()->with('success', 'Tagihan berhasil dihapus.');
    }

    /** Bulk delete tagihan */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $institutionId = $this->institutionId($request);

        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:bills,id'],
        ], [
            'ids.required' => 'Pilih minimal 1 tagihan untuk dihapus.',
        ]);

        $dihapus  = 0;
        $dilewati = 0;

        foreach ($request->ids as $id) {
            $bill = Bill::forInstitution($institutionId)->find($id);
            if (! $bill) continue;

            if ($bill->nominal_terbayar > 0) {
                $dilewati++;
                continue;
            }

            $bill->delete();
            $dihapus++;
        }

        $pesan = "{$dihapus} tagihan berhasil dihapus";
        if ($dilewati > 0) {
            $pesan .= ", {$dilewati} dilewati karena sudah memiliki pembayaran.";
        } else {
            $pesan .= '.';
        }

        return redirect()->route('bills.index')->with('success', $pesan);
    }

    private function authorizeBill(Bill $bill, Request $request): void
    {
        $user = $request->user();
        if (! $user->hasRole('Super Admin') && $bill->institution_id !== (int) $user->institution_id) {
            abort(403);
        }
    }
}
