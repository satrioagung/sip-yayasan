@extends('layouts.app')
@section('title', 'Tambah Kelas')
@section('page-title', 'Tambah Kelas Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('classes.store') }}" class="space-y-5">
        @csrf
        @include('classes._form', ['class' => null, 'tahunAjaran' => $tahunAjaran])
        <div class="flex justify-end gap-3">
            <a href="{{ route('classes.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Kelas
            </button>
        </div>
    </form>
</div>
@endsection
