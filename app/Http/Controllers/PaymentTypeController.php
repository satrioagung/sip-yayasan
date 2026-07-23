<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use App\Models\SchoolYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentTypeController extends Controller
{
    private function institutionId(Request $request): int
    {
        $user = $request->user();
        if ($user->hasRole('Super Admin')) {
            return (int) ($request->session()->get('active_tenant_id') ?? abort(403, 'Pilih lembaga aktif terlebih dahulu.'));
        }
        return (int) $user->institution_id;
    }

    public function index(Request $request): View
    {
        $institutionId = $this->institutionId($request);

        $types = PaymentType::with('schoolYear')
            ->forInstitution($institutionId)
            ->when($request->search, fn($q) =>
                $q->where('nama', 'ilike', "%{$request->search}%")
                  ->orWhere('kode', 'ilike', "%{$request->search}%")
            )
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        return view('payment-types.index', compact('types'));
    }

    public function create(Request $request): View
    {
        $institutionId = $this->institutionId($request);
        $schoolYears   = SchoolYear::where('institution_id', $institutionId)->orderByDesc('tanggal_mulai')->get();

        return view('payment-types.create', compact('schoolYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $institutionId = $this->institutionId($request);
        $data = $this->validatedData($request, $institutionId);
        $data['institution_id'] = $institutionId;

        PaymentType::create($data);

        return redirect()->route('payment-types.index')
            ->with('success', "Jenis pembayaran \"{$data['nama']}\" berhasil ditambahkan.");
    }

    public function edit(Request $request, PaymentType $paymentType): View
    {
        $this->authorizeType($paymentType, $request);
        $institutionId = $this->institutionId($request);
        $schoolYears   = SchoolYear::where('institution_id', $institutionId)->orderByDesc('tanggal_mulai')->get();

        return view('payment-types.edit', compact('paymentType', 'schoolYears'));
    }

    public function update(Request $request, PaymentType $paymentType): RedirectResponse
    {
        $this->authorizeType($paymentType, $request);
        $institutionId = $paymentType->institution_id;
        $data = $this->validatedData($request, $institutionId, $paymentType->id);

        $paymentType->update($data);

        return redirect()->route('payment-types.index')
            ->with('success', "Jenis pembayaran \"{$paymentType->nama}\" berhasil diperbarui.");
    }

    public function destroy(PaymentType $paymentType, Request $request): RedirectResponse
    {
        $this->authorizeType($paymentType, $request);
        $nama = $paymentType->nama;

        if ($paymentType->bills()->exists()) {
            return back()->with('error', "Jenis pembayaran \"{$nama}\" tidak dapat dihapus karena sudah memiliki data tagihan.");
        }

        $paymentType->delete();

        return redirect()->route('payment-types.index')
            ->with('success', "Jenis pembayaran \"{$nama}\" berhasil dihapus.");
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $institutionId = $this->institutionId($request);

        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:payment_types,id'],
        ], [
            'ids.required' => 'Pilih minimal 1 jenis pembayaran untuk dihapus.',
        ]);

        $dihapus  = 0;
        $dilewati = 0;

        foreach ($request->ids as $id) {
            $type = PaymentType::forInstitution($institutionId)->find($id);
            if (! $type) continue;

            if ($type->bills()->exists()) {
                $dilewati++;
                continue;
            }

            $type->delete();
            $dihapus++;
        }

        $pesan = "{$dihapus} jenis pembayaran berhasil dihapus";
        if ($dilewati > 0) {
            $pesan .= ", {$dilewati} dilewati karena sudah memiliki tagihan.";
        } else {
            $pesan .= '.';
        }

        return redirect()->route('payment-types.index')->with('success', $pesan);
    }

    private function validatedData(Request $request, int $institutionId, ?int $excludeId = null): array
    {
        $uniqueKode = Rule::unique('payment_types', 'kode')
            ->where('institution_id', $institutionId)
            ->whereNull('deleted_at');

        if ($excludeId !== null) {
            $uniqueKode->ignore($excludeId);
        }

        return $request->validate([
            'nama'            => ['required', 'string', 'max:100'],
            'kode'            => ['required', 'string', 'max:20', 'alpha_num', $uniqueKode],
            'nominal_default' => ['required', 'integer', 'min:0'],
            'tipe'            => ['required', 'in:bulanan,tahunan,sekali,bebas'],
            'bisa_cicil'      => ['boolean'],
            'wajib'           => ['boolean'],
            'aktif'           => ['boolean'],
            'school_year_id'  => ['nullable', 'exists:school_years,id'],
            'keterangan'      => ['nullable', 'string', 'max:500'],
        ], [
            'nama.required'            => 'Nama jenis pembayaran wajib diisi.',
            'kode.required'            => 'Kode wajib diisi.',
            'kode.alpha_num'           => 'Kode hanya boleh berisi huruf dan angka (tanpa spasi).',
            'kode.unique'              => 'Kode sudah digunakan oleh jenis pembayaran lain.',
            'nominal_default.required' => 'Nominal wajib diisi.',
            'nominal_default.min'      => 'Nominal tidak boleh negatif.',
            'tipe.required'            => 'Tipe pembayaran wajib dipilih.',
            'tipe.in'                  => 'Tipe pembayaran tidak valid.',
        ]);
    }

    private function authorizeType(PaymentType $type, Request $request): void
    {
        $user = $request->user();
        if (! $user->hasRole('Super Admin') && $type->institution_id !== (int) $user->institution_id) {
            abort(403);
        }
    }
}
