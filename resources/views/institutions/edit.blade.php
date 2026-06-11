@extends('layouts.app')

@section('title', 'Ubah Lembaga — ' . $institution->name)
@section('page-title', 'Ubah Data Lembaga')
@section('breadcrumb', 'Beranda / Lembaga / Ubah')

@section('content')
<div class="max-w-3xl mx-auto">

    <form method="POST" action="{{ route('institutions.update', $institution) }}"
          enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        @include('institutions._form', ['institution' => $institution, 'jenjang' => $jenjang])

        {{-- Tombol Aksi --}}
        <div class="flex items-center justify-between pt-2">
            <form method="POST" action="{{ route('institutions.destroy', $institution) }}"
                  onsubmit="return confirm('Yakin ingin menghapus lembaga \"{{ addslashes($institution->name) }}\"?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Lembaga
                </button>
            </form>

            <div class="flex items-center gap-3">
                <a href="{{ route('institutions.index') }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
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
