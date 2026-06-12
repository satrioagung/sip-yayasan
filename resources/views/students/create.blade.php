@extends('layouts.app')
@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa Baru')
@section('breadcrumb', 'Beranda / Data Siswa / Tambah')

@section('content')
<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('students.store') }}" class="space-y-5">
        @csrf
        @include('students._form', ['student' => null, 'kelas' => $kelas])
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('students.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Siswa
            </button>
        </div>
    </form>
</div>
@endsection
