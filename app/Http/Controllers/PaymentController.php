<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\ReceiptTemplate;
use App\Models\SchoolClass;
use App\Services\PaymentService;
use App\Services\ReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private ReceiptService $receiptService,
    ) {}

    private function institutionId(Request $request): int
    {
        $user = $request->user();
        if ($user->hasRole('Super Admin')) {
            return (int) ($request->session()->get('active_tenant_id')
                ?? abort(403, 'Pilih lembaga aktif terlebih dahulu.'));
        }
        return (int) $user->institution_id;
    }

    /** ============================================================
     *  DAFTAR PEMBAYARAN
     * ============================================================ */
    public function index(Request $request): View
    {
        $institutionId = $this->institutionId($request);

        $payments = Payment::with(['student.class', 'bill.paymentType', 'petugas'])
            ->forInstitution($institutionId)
            ->when($request->search, function ($q) use ($request) {
                $s = $request->search;
                $q->where('nomor_transaksi', 'ilike', "%{$s}%")
                  ->orWhereHas('student', fn ($sq) =>
                      $sq->where('nama_lengkap', 'ilike', "%{$s}%")
                         ->orWhere('nis', 'ilike', "%{$s}%")
                  );
            })
            ->when($request->metode_bayar, fn ($q) => $q->where('metode_bayar', $request->metode_bayar))
            ->when($request->class_id, fn ($q) =>
                $q->whereHas('student', fn ($sq) => $sq->where('class_id', $request->class_id))
            )
            ->when($request->tanggal_dari && $request->tanggal_sampai, fn ($q) =>
                $q->whereBetween('tanggal_bayar', [
                    \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal_dari),
                    \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal_sampai),
                ])
            )
            ->orderByDesc('tanggal_bayar')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $kelas = SchoolClass::where('institution_id', $institutionId)->aktif()->orderBy('nama_kelas')->get();

        // Stat total pembayaran hari ini dan bulan ini
        $totalHariIni  = Payment::forInstitution($institutionId)->whereDate('tanggal_bayar', today())->sum('nominal_bayar');
        $totalBulanIni = Payment::forInstitution($institutionId)->whereMonth('tanggal_bayar', now()->month)->whereYear('tanggal_bayar', now()->year)->sum('nominal_bayar');
        $totalKeseluruhan = Payment::forInstitution($institutionId)->sum('nominal_bayar');

        return view('payments.index', compact('payments', 'kelas', 'totalHariIni', 'totalBulanIni', 'totalKeseluruhan'));
    }

    /** ============================================================
     *  FORM INPUT PEMBAYARAN
     * ============================================================ */
    public function create(Request $request): View
    {
        $institutionId = $this->institutionId($request);

        // Jika ada bill_id dari query, pre-fill
        $bill = null;
        if ($request->bill_id) {
            $bill = Bill::with(['student.class', 'paymentType'])
                ->forInstitution($institutionId)
                ->findOrFail($request->bill_id);
        }

        // Daftar tagihan belum lunas untuk dropdown
        $bills = Bill::with(['student', 'paymentType'])
            ->forInstitution($institutionId)
            ->whereIn('status', ['belum_bayar', 'sebagian'])
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        return view('payments.create', compact('bill', 'bills'));
    }

    /** ============================================================
     *  SIMPAN PEMBAYARAN
     * ============================================================ */
    public function store(Request $request): RedirectResponse
    {
        $institutionId = $this->institutionId($request);

        $request->validate([
            'bill_id'       => ['required', 'exists:bills,id'],
            'tanggal_bayar' => ['required', 'date_format:d/m/Y'],
            'metode_bayar'  => ['required', 'in:tunai,transfer,qris,kartu_debit,kartu_kredit,lainnya'],
            'nominal_bayar' => ['required', 'integer', 'min:1'],
            'bukti_file'    => ['nullable', 'file', 'image', 'max:2048'],
            'keterangan'    => ['nullable', 'string', 'max:500'],
        ], [
            'bill_id.required'       => 'Pilih tagihan yang akan dibayar.',
            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi.',
            'nominal_bayar.required' => 'Nominal pembayaran wajib diisi.',
            'nominal_bayar.min'      => 'Nominal harus lebih dari 0.',
            'bukti_file.image'       => 'Bukti transfer harus berupa gambar (JPG/PNG).',
            'bukti_file.max'         => 'Ukuran bukti transfer maksimal 2 MB.',
        ]);

        $bill = Bill::forInstitution($institutionId)->findOrFail($request->bill_id);

        // Validasi: nominal tidak boleh melebihi sisa tagihan
        $sisa = $bill->nominal - $bill->nominal_terbayar;
        if ((int) $request->nominal_bayar > $sisa) {
            return back()->withInput()
                ->withErrors(['nominal_bayar' => "Nominal melebihi sisa tagihan (Rp " . number_format($sisa, 0, ',', '.') . ")."]);
        }

        $payment = $this->paymentService->pay(
            $bill,
            array_merge($request->only(['tanggal_bayar', 'metode_bayar', 'nominal_bayar', 'keterangan']), [
                'petugas_id' => $request->user()->id,
            ]),
            $request->hasFile('bukti_file') ? $request->file('bukti_file') : null,
        );

        return redirect()->route('payments.show', $payment)
            ->with('success', "Pembayaran berhasil dicatat. Nomor Transaksi: {$payment->nomor_transaksi}");
    }

    /** ============================================================
     *  DETAIL PEMBAYARAN
     * ============================================================ */
    public function show(Payment $payment, Request $request): View
    {
        $this->authorizePay($payment, $request);
        $payment->load(['bill.paymentType', 'bill.schoolYear', 'student.class', 'petugas', 'institution']);

        $templates = ReceiptTemplate::where('institution_id', $payment->institution_id)
            ->orderByDesc('is_default')
            ->orderBy('nama_template')
            ->get();

        return view('payments.show', compact('payment', 'templates'));
    }

    /** ============================================================
     *  CETAK STRUK PDF
     * ============================================================ */
    public function cetakStruk(Payment $payment, Request $request)
    {
        $this->authorizePay($payment, $request);

        $templateId = $request->template_id;
        $template   = $templateId
            ? ReceiptTemplate::findOrFail($templateId)
            : null;

        $pdf = $this->receiptService->generatePdf($payment, $template);

        $filename = "struk-{$payment->nomor_transaksi}.pdf";
        $filename = str_replace('/', '-', $filename);

        return $pdf->download($filename);
    }

    /** ============================================================
     *  BATAL / HAPUS PEMBAYARAN
     * ============================================================ */
    public function destroy(Payment $payment, Request $request): RedirectResponse
    {
        $this->authorizePay($payment, $request);

        $nomorTrx = $payment->nomor_transaksi;
        $this->paymentService->cancel($payment);

        return redirect()->route('payments.index')
            ->with('success', "Pembayaran {$nomorTrx} berhasil dibatalkan dan status tagihan sudah di-rollback.");
    }

    private function authorizePay(Payment $payment, Request $request): void
    {
        $user = $request->user();
        if (! $user->hasRole('Super Admin') && $payment->institution_id !== (int) $user->institution_id) {
            abort(403);
        }
    }
}
