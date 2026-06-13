@extends('layouts.app')
@section('title', 'Ubah Jenis Pembayaran')
@section('page-title', 'Ubah Jenis Pembayaran')
@section('breadcrumb', 'Beranda / Jenis Pembayaran / Ubah')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Form Update --}}
    <form id="form-update" method="POST" action="{{ route('payment-types.update', $paymentType) }}" class="space-y-5">
        @csrf @method('PUT')
        @include('payment-types._form', ['paymentType' => $paymentType, 'schoolYears' => $schoolYears])
        <div class="flex items-center justify-between pt-2">
            {{-- Tombol Hapus: terhubung ke form-delete di bawah --}}
            <button type="submit" form="form-delete" class="btn-danger">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus
            </button>
            <div class="flex gap-3">
                <a href="{{ route('payment-types.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" form="form-update" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    {{-- Form Delete (terpisah, di luar form-update) --}}
    <form id="form-delete" method="POST" action="{{ route('payment-types.destroy', $paymentType) }}"
          data-confirm="Hapus jenis pembayaran &quot;{{ $paymentType->nama }}&quot;? Tidak dapat dihapus jika sudah ada tagihan.">
        @csrf @method('DELETE')
    </form>
</div>
@endsection

