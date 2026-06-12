@extends('layouts.app')
@section('title', 'Tambah Tahun Ajaran')
@section('page-title', 'Tambah Tahun Ajaran')

@section('content')
<div class="max-w-lg mx-auto">
    <form method="POST" action="{{ route('school-years.store') }}" class="space-y-5">
        @csrf
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3">Data Tahun Ajaran</h3>

            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Tahun Ajaran <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                       placeholder="contoh: 2024/2025"
                       class="form-input {{ $errors->has('nama') ? 'form-input-error' : '' }}">
                @error('nama') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                           class="form-input {{ $errors->has('tanggal_mulai') ? 'form-input-error' : '' }}">
                    @error('tanggal_mulai') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                           class="form-input {{ $errors->has('tanggal_selesai') ? 'form-input-error' : '' }}">
                    @error('tanggal_selesai') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="flex items-center gap-3 cursor-pointer" x-data="{ on: {{ old('aktif') ? 'true' : 'false' }} }">
                <div class="relative">
                    <input type="hidden" name="aktif" :value="on ? '1' : '0'">
                    <button type="button" @click="on = !on"
                            :class="on ? 'bg-blue-600' : 'bg-gray-200'"
                            class="w-10 h-5 rounded-full transition-colors focus:outline-none">
                        <span :class="on ? 'translate-x-5' : 'translate-x-1'"
                              class="inline-block w-4 h-4 bg-white rounded-full shadow transform transition-transform"></span>
                    </button>
                </div>
                <span class="text-sm font-medium text-gray-700">Jadikan Tahun Ajaran Aktif</span>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('school-years.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
