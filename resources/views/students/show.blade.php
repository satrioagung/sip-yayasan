@extends('layouts.app')
@section('title', 'Detail Siswa')
@section('page-title', 'Detail Siswa')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
        <div class="p-6 flex items-center gap-5">
            <img src="{{ $student->foto_url }}" alt="{{ $student->nama_lengkap }}"
                 class="w-20 h-20 rounded-2xl object-cover shadow-md flex-shrink-0">
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $student->nama_lengkap }}</h2>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg">{{ $student->nis }}</span>
                            @if($student->nisn)
                                <span class="font-mono text-xs bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg">NISN: {{ $student->nisn }}</span>
                            @endif
                            @if($student->aktif)
                                <span class="badge-green"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif</span>
                            @else
                                <span class="badge-gray"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('students.edit', $student) }}" class="btn-secondary text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Ubah
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Data Pribadi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Data Pribadi</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Jenis Kelamin</span>
                    <span class="font-medium">{{ $student->jenis_kelamin_label }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tempat, Tgl Lahir</span>
                    <span class="font-medium text-right">
                        {{ $student->tempat_lahir ?? '—' }}
                        @if($student->tanggal_lahir), {{ $student->tanggal_lahir->format('d/m/Y') }} @endif
                    </span>
                </div>
                <div class="flex justify-between items-start gap-4">
                    <span class="text-gray-500 flex-shrink-0">Alamat</span>
                    <span class="font-medium text-right">{{ $student->alamat ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Data Kelas & Ortu --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Kelas & Orang Tua</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Kelas</span>
                    <span class="font-medium">{{ $student->class?->nama_kelas ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tahun Ajaran</span>
                    <span class="font-medium">{{ $student->class?->schoolYear?->nama ?? '—' }}</span>
                </div>
                <hr class="border-gray-100">
                <div class="flex justify-between">
                    <span class="text-gray-500">Nama Ortu/Wali</span>
                    <span class="font-medium">{{ $student->nama_ortu ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">No. HP Ortu</span>
                    <span class="font-medium font-mono">{{ $student->no_hp_ortu ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('students.index') }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
        <a href="{{ route('students.edit', $student) }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Ubah Data
        </a>
    </div>
</div>
@endsection
