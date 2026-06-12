@extends('layouts.app')
@section('title', 'Ubah Data Siswa')
@section('page-title', 'Ubah Data Siswa')
@section('breadcrumb', 'Beranda / Data Siswa / Ubah')

@section('content')
<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('students.update', $student) }}" class="space-y-5">
        @csrf @method('PUT')
        @include('students._form', ['student' => $student, 'kelas' => $kelas])
        <div class="flex justify-between pt-2">
            <form method="POST" action="{{ route('students.destroy', $student) }}"
                  data-confirm="Hapus siswa &quot;{{ $student->nama_lengkap }}&quot;? Data siswa dan semua catatan pembayaran tidak dapat dikembalikan.">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Siswa
                </button>
            </form>
            <div class="flex gap-3">
                <a href="{{ route('students.index') }}" class="btn-secondary">Batal</a>
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
