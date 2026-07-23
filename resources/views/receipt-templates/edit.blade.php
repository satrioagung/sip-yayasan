@extends('layouts.app')
@section('title', 'Ubah Template Struk')
@section('page-title', 'Ubah Template Struk')
@section('breadcrumb', 'Beranda / Template Struk / Ubah')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('receipt-templates.update', $receiptTemplate) }}" class="space-y-5">
        @csrf @method('PUT')
        @include('receipt-templates._form', ['t' => $receiptTemplate])
        <div class="flex justify-between">
            <form method="POST" action="{{ route('receipt-templates.destroy', $receiptTemplate) }}"
                  data-confirm="Hapus template &quot;{{ $receiptTemplate->nama_template }}&quot;?">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </form>
            <div class="flex gap-3">
                <a href="{{ route('receipt-templates.preview', $receiptTemplate) }}" target="_blank"
                   class="btn-secondary">Preview</a>
                <a href="{{ route('receipt-templates.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
