@extends('layouts.app')
@section('title', 'Preview Template — ' . $receiptTemplate->nama_template)
@section('page-title', 'Preview Template Struk')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-800">{{ $receiptTemplate->nama_template }}</h2>
            <p class="text-sm text-gray-500">{{ $receiptTemplate->ukuran_label }}</p>
        </div>
        <a href="{{ route('receipt-templates.edit', $receiptTemplate) }}" class="btn-secondary">
            ← Kembali ke Editor
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 mb-5">
            ⚠️ Ini adalah preview dengan data dummy. Tampilan sesungguhnya mungkin sedikit berbeda karena render PDF DomPDF.
        </p>

        {{-- Simulasi struk --}}
        <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 {{ $receiptTemplate->ukuran !== 'a4' ? 'max-w-xs mx-auto' : '' }}"
             style="font-family: monospace; font-size: {{ $receiptTemplate->ukuran !== 'a4' ? '11px' : '13px' }};">

            {{-- Header --}}
            @if($headerHtml)
                {!! $headerHtml !!}
            @else
                <div style="text-align:center;">
                    <div style="font-size:1.1em;font-weight:bold;">SMA Contoh Bangsa</div>
                    <div style="font-size:0.85em;color:#666;">Jl. Pendidikan No. 1, Kota</div>
                </div>
            @endif

            <hr style="border-top:2px solid #333;margin:10px 0;">
            <div style="text-align:center;font-weight:bold;text-transform:uppercase;letter-spacing:1px;">Bukti Pembayaran SPP</div>
            <hr style="border-top:1px dashed #ccc;margin:8px 0;">

            {{-- Nominal --}}
            <div style="text-align:center;background:#f0f4ff;padding:10px;border-radius:4px;margin:10px 0;">
                <div style="font-size:0.8em;color:#555;">NOMINAL PEMBAYARAN</div>
                <div style="font-size:1.6em;font-weight:bold;color:#1d4ed8;">Rp 250.000</div>
                <span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:20px;font-size:0.75em;font-weight:bold;">✓ LUNAS</span>
            </div>

            <table style="width:100%;border-collapse:collapse;font-size:0.9em;">
                <tr><td style="color:#555;padding:2px 0;width:40%;">No. Transaksi</td><td style="padding:2px 0;width:5%;text-align:center;">:</td><td style="font-weight:bold;">SMK/TRX/2024/00001</td></tr>
                <tr><td style="color:#555;padding:2px 0;">Tanggal</td><td style="text-align:center;">:</td><td>{{ now()->translatedFormat('d F Y') }}</td></tr>
                <tr><td style="color:#555;padding:2px 0;">Metode</td><td style="text-align:center;">:</td><td>Tunai</td></tr>
            </table>
            <hr style="border-top:1px dashed #ccc;margin:8px 0;">
            <table style="width:100%;border-collapse:collapse;font-size:0.9em;">
                <tr><td style="color:#555;padding:2px 0;width:40%;">Nama Siswa</td><td style="padding:2px 0;width:5%;text-align:center;">:</td><td>Budi Santoso</td></tr>
                <tr><td style="color:#555;padding:2px 0;">NIS</td><td style="text-align:center;">:</td><td>2024001</td></tr>
                <tr><td style="color:#555;padding:2px 0;">Kelas</td><td style="text-align:center;">:</td><td>X RPL 1</td></tr>
            </table>
            <hr style="border-top:1px dashed #ccc;margin:8px 0;">
            <table style="width:100%;border-collapse:collapse;font-size:0.9em;">
                <tr><td style="color:#555;padding:2px 0;width:40%;">Jenis Pembayaran</td><td style="padding:2px 0;width:5%;text-align:center;">:</td><td>SPP Bulanan</td></tr>
                <tr><td style="color:#555;padding:2px 0;">Periode</td><td style="text-align:center;">:</td><td>Juli 2024</td></tr>
                <tr><td style="color:#555;padding:2px 0;">Total Tagihan</td><td style="text-align:center;">:</td><td>Rp 250.000</td></tr>
            </table>

            <hr style="border-top:2px solid #333;margin:10px 0;">

            {{-- Footer --}}
            @if($footerHtml)
                {!! $footerHtml !!}
            @else
                <div style="text-align:center;font-size:0.8em;color:#666;margin-top:8px;">
                    Struk ini adalah bukti pembayaran yang sah.<br>Terima kasih atas kepercayaan Anda.<br>
                    Dicetak: {{ now('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
