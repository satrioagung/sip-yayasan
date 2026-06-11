@extends('layouts.app')

@section('title', 'Tambah Lembaga')
@section('page-title', 'Tambah Lembaga Baru')
@section('breadcrumb', 'Beranda / Lembaga / Tambah')

@section('content')
<div class="max-w-3xl mx-auto">

    <form method="POST" action="{{ route('institutions.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        @include('institutions._form', ['institution' => null, 'jenjang' => $jenjang])

        {{-- Tombol Aksi --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('institutions.index') }}"
               class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Lembaga
            </button>
        </div>
    </form>

</div>
@endsection
