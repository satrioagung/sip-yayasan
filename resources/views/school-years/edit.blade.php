@extends('layouts.app')
@section('title', 'Ubah Tahun Ajaran')
@section('page-title', 'Ubah Tahun Ajaran')
@section('content')
<div class="max-w-lg mx-auto">
    <form method="POST" action="{{ route('school-years.update', $schoolYear) }}" class="space-y-5">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-semibold text-gray-700 border-b border-gray-100 pb-3">Data Tahun Ajaran</h3>

            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Tahun Ajaran <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama" name="nama"
                    value="{{ old('nama', $schoolYear->nama) }}"
                    class="form-input {{ $errors->has('nama') ? 'form-input-error' : '' }}">
                @error('nama') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                        value="{{ old('tanggal_mulai', $schoolYear->tanggal_mulai->format('Y-m-d')) }}"
                        class="form-input">
                </div>
                <div>
                    <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Selesai</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                        value="{{ old('tanggal_selesai', $schoolYear->tanggal_selesai->format('Y-m-d')) }}"
                        class="form-input">
                    @error('tanggal_selesai') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            @php $aktifDefault = old('aktif', $schoolYear->aktif) ? '1' : '0'; @endphp

            <label class="flex items-center gap-3 cursor-pointer"
                x-data="{ on: {{ $aktifDefault === '1' ? 'true' : 'false' }} }"
                x-init="$watch('on', val => $refs.aktifInput.value = val ? '1' : '0')">
                <div class="relative">
                    <input type="hidden" name="aktif" x-ref="aktifInput" value="{{ $aktifDefault }}">
                    <button type="button" @click="on = !on"
                        :class="on ? 'bg-blue-600' : 'bg-gray-200'"
                        class="relative w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                        <span
                            :class="on ? 'translate-x-[23px]' : 'translate-x-[3px]'"
                            class="absolute top-1 left-0 w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200">
                        </span>
                    </button>
                </div>
                <span class="text-sm font-medium text-gray-700">Tahun Ajaran Aktif</span>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('school-years.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection