<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $payment->nomor_transaksi }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @php
            $isThermal = in_array($ukuran, ['thermal58', 'thermal80']);
            $fontSize  = $isThermal ? '9px' : '11px';
            $fontHead  = $isThermal ? '11px' : '14px';
        @endphp

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: {{ $fontSize }};
            color: #111;
            background: #fff;
            padding: {{ $isThermal ? '6px' : '20px' }};
        }

        .header { text-align: center; margin-bottom: {{ $isThermal ? '6px' : '16px' }}; }
        .logo { max-width: {{ $isThermal ? '50px' : '80px' }}; max-height: {{ $isThermal ? '50px' : '80px' }}; margin-bottom: 4px; }
        .school-name { font-size: {{ $isThermal ? '12px' : '16px' }}; font-weight: bold; }
        .school-sub { font-size: {{ $isThermal ? '8px' : '10px' }}; color: #555; margin-top: 2px; }

        .divider { border: none; border-top: {{ $isThermal ? '1px dashed #999' : '2px solid #333' }}; margin: {{ $isThermal ? '5px 0' : '12px 0' }}; }
        .divider-light { border: none; border-top: 1px dashed #ccc; margin: {{ $isThermal ? '4px 0' : '8px 0' }}; }

        .title { text-align: center; font-size: {{ $isThermal ? '10px' : '13px' }}; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }

        table.info { width: 100%; border-collapse: collapse; margin: {{ $isThermal ? '5px 0' : '10px 0' }}; }
        table.info td { padding: {{ $isThermal ? '1.5px 2px' : '3px 4px' }}; vertical-align: top; }
        table.info td.label { color: #555; width: {{ $isThermal ? '42%' : '35%' }}; font-size: {{ $isThermal ? '8px' : '10px' }}; }
        table.info td.colon { width: 8px; text-align: center; }
        table.info td.value { font-weight: 500; }

        .nominal-box {
            text-align: center;
            margin: {{ $isThermal ? '6px 0' : '14px 0' }};
            padding: {{ $isThermal ? '5px' : '12px' }};
            background: #f0f4ff;
            border-radius: 4px;
        }
        .nominal-label { font-size: {{ $isThermal ? '8px' : '10px' }}; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
        .nominal-value { font-size: {{ $isThermal ? '16px' : '24px' }}; font-weight: bold; color: #1d4ed8; margin-top: 2px; }

        .status-badge {
            display: inline-block;
            padding: {{ $isThermal ? '2px 6px' : '3px 10px' }};
            border-radius: 20px;
            font-size: {{ $isThermal ? '8px' : '9px' }};
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-lunas    { background: #dcfce7; color: #166534; }
        .status-sebagian { background: #fef9c3; color: #854d0e; }
        .status-belum    { background: #fee2e2; color: #991b1b; }

        .footer-text { text-align: center; font-size: {{ $isThermal ? '7px' : '9px' }}; color: #666; margin-top: {{ $isThermal ? '6px' : '14px' }}; }
        .signature-area { margin-top: {{ $isThermal ? '12px' : '24px' }}; }
        .signature-box { display: inline-block; text-align: center; }
        .signature-line { border-top: 1px solid #555; width: {{ $isThermal ? '60px' : '120px' }}; margin-top: {{ $isThermal ? '18px' : '35px' }}; }
        .signature-label { font-size: {{ $isThermal ? '7px' : '9px' }}; color: #555; margin-top: 2px; }

        .custom-header { margin-bottom: {{ $isThermal ? '4px' : '10px' }}; }
        .custom-footer { margin-top: {{ $isThermal ? '4px' : '10px' }}; }

        @if(!$isThermal)
        .page-border {
            border: 2px solid #1d4ed8;
            padding: 16px;
            border-radius: 6px;
        }
        @endif
    </style>
</head>
<body>

@if(!$isThermal)
<div class="page-border">
@endif

{{-- ===== HEADER ===== --}}
@if($headerHtml)
    <div class="custom-header">{!! $headerHtml !!}</div>
    <hr class="divider">
@else
    <div class="header">
        @if(($template?->show_logo ?? true) && $payment->institution?->logo)
            <img src="{{ storage_path('app/public/' . $payment->institution->logo) }}"
                 class="logo" alt="Logo">
        @endif
        <div class="school-name">{{ $payment->institution?->name ?? 'Lembaga Pendidikan' }}</div>
        <div class="school-sub">{{ $payment->institution?->address ?? '' }}</div>
        @if($payment->institution?->phone)
            <div class="school-sub">Telp: {{ $payment->institution->phone }}</div>
        @endif
    </div>
    <hr class="divider">
@endif

{{-- ===== JUDUL --}}
<div class="title">Bukti Pembayaran SPP</div>
<hr class="divider-light">

{{-- ===== NOMINAL BAYAR ===== --}}
<div class="nominal-box">
    <div class="nominal-label">Nominal Pembayaran</div>
    <div class="nominal-value">{{ $payment->nominal_bayar_format }}</div>
    @php $billStatus = $payment->bill?->status; @endphp
    <div style="margin-top:4px;">
        <span class="status-badge {{ $billStatus === 'lunas' ? 'status-lunas' : ($billStatus === 'sebagian' ? 'status-sebagian' : 'status-belum') }}">
            {{ $billStatus === 'lunas' ? '✓ LUNAS' : ($billStatus === 'sebagian' ? '⚡ SEBAGIAN' : '⏳ BELUM LUNAS') }}
        </span>
    </div>
</div>

{{-- ===== INFO TRANSAKSI ===== --}}
<table class="info">
    <tr>
        <td class="label">No. Transaksi</td>
        <td class="colon">:</td>
        <td class="value"><strong>{{ $payment->nomor_transaksi }}</strong></td>
    </tr>
    <tr>
        <td class="label">Tanggal Bayar</td>
        <td class="colon">:</td>
        <td class="value">{{ $payment->tanggal_bayar_format }}</td>
    </tr>
    <tr>
        <td class="label">Metode Bayar</td>
        <td class="colon">:</td>
        <td class="value">{{ $payment->metode_bayar_label }}</td>
    </tr>
</table>

<hr class="divider-light">

{{-- ===== INFO SISWA ===== --}}
<table class="info">
    <tr>
        <td class="label">Nama Siswa</td>
        <td class="colon">:</td>
        <td class="value">{{ $payment->student?->nama_lengkap }}</td>
    </tr>
    <tr>
        <td class="label">NIS</td>
        <td class="colon">:</td>
        <td class="value">{{ $payment->student?->nis ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Kelas</td>
        <td class="colon">:</td>
        <td class="value">{{ $payment->student?->class?->nama_kelas ?? '-' }}</td>
    </tr>
</table>

<hr class="divider-light">

{{-- ===== INFO TAGIHAN ===== --}}
<table class="info">
    <tr>
        <td class="label">Jenis Pembayaran</td>
        <td class="colon">:</td>
        <td class="value">{{ $payment->bill?->paymentType?->nama ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Periode</td>
        <td class="colon">:</td>
        <td class="value">{{ $payment->bill?->periode ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Total Tagihan</td>
        <td class="colon">:</td>
        <td class="value">{{ 'Rp ' . number_format($payment->bill?->nominal ?? 0, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="label">Total Terbayar</td>
        <td class="colon">:</td>
        <td class="value">{{ 'Rp ' . number_format($payment->bill?->nominal_terbayar ?? 0, 0, ',', '.') }}</td>
    </tr>
    @if(($payment->bill?->nominal ?? 0) > ($payment->bill?->nominal_terbayar ?? 0))
    <tr>
        <td class="label">Sisa Tagihan</td>
        <td class="colon">:</td>
        <td class="value" style="color:#dc2626;font-weight:bold;">
            {{ 'Rp ' . number_format(($payment->bill?->nominal ?? 0) - ($payment->bill?->nominal_terbayar ?? 0), 0, ',', '.') }}
        </td>
    </tr>
    @endif
</table>

@if($payment->keterangan)
<hr class="divider-light">
<table class="info">
    <tr>
        <td class="label">Keterangan</td>
        <td class="colon">:</td>
        <td class="value">{{ $payment->keterangan }}</td>
    </tr>
</table>
@endif

<hr class="divider">

{{-- ===== TANDA TANGAN ===== --}}
@if(!$isThermal)
<div class="signature-area" style="display:flex;justify-content:space-between;padding:0 10px;">
    <div class="signature-box">
        <div style="font-size:9px;color:#555;">Orang Tua/Wali</div>
        <div class="signature-line"></div>
        <div class="signature-label">(_________________)</div>
    </div>
    <div class="signature-box" style="text-align:center;">
        <div style="font-size:9px;color:#555;">Petugas</div>
        <div class="signature-line"></div>
        <div class="signature-label">{{ $payment->petugas?->name ?? 'Admin' }}</div>
    </div>
</div>
@else
<div style="text-align:right;font-size:8px;color:#555;margin-top:4px;">
    Petugas: {{ $payment->petugas?->name ?? 'Admin' }}
</div>
@endif

{{-- ===== FOOTER ===== --}}
@if($footerHtml)
    <hr class="divider-light">
    <div class="custom-footer">{!! $footerHtml !!}</div>
@else
    <div class="footer-text">
        @if($payment->institution?->footer_struk)
            {{ $payment->institution->footer_struk }}
        @else
            Struk ini adalah bukti pembayaran yang sah.<br>Terima kasih atas kepercayaan Anda.
        @endif
        <br><br>Dicetak: {{ now('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
    </div>
@endif

@if(!$isThermal)
</div>
@endif

</body>
</html>
