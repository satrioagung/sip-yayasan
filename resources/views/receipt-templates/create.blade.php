@extends('layouts.app')
@section('title', 'Tambah Template Struk')
@section('page-title', 'Tambah Template Struk')
@section('breadcrumb', 'Beranda / Template Struk / Tambah')

@section('content')
<div class="max-w-2xl mx-auto">
    <form method="POST" action="{{ route('receipt-templates.store') }}" class="space-y-5">
        @csrf
        @include('receipt-templates._form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('receipt-templates.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Template
            </button>
        </div>
    </form>
</div>
@endsection
