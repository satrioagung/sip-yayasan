<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\ReceiptTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class ReceiptService
{
    /**
     * Render placeholder dalam template.
     * Placeholder yang didukung:
     *   {{ school_name }}, {{ student_name }}, {{ student_nis }},
     *   {{ payment_type }}, {{ amount }}, {{ amount_words }},
     *   {{ payment_date }}, {{ nomor_transaksi }}, {{ metode_bayar }},
     *   {{ periode }}, {{ petugas }}, {{ remaining }}
     */
    public function resolvePlaceholders(string $text, Payment $payment): string
    {
        $bill        = $payment->bill;
        $institution = $payment->institution ?? $bill->institution;

        $map = [
            '{{ school_name }}'       => $institution?->nama_lengkap ?? $institution?->name ?? 'Lembaga',
            '{{ student_name }}'      => $payment->student?->nama_lengkap ?? '-',
            '{{ student_nis }}'       => $payment->student?->nis ?? '-',
            '{{ student_class }}'     => $payment->student?->class?->nama_kelas ?? '-',
            '{{ payment_type }}'      => $bill?->paymentType?->nama ?? '-',
            '{{ amount }}'            => 'Rp ' . number_format($payment->nominal_bayar, 0, ',', '.'),
            '{{ total_amount }}'      => 'Rp ' . number_format($bill?->nominal ?? 0, 0, ',', '.'),
            '{{ amount_paid }}'       => 'Rp ' . number_format($bill?->nominal_terbayar ?? 0, 0, ',', '.'),
            '{{ remaining }}'         => 'Rp ' . number_format(max(0, ($bill?->nominal ?? 0) - ($bill?->nominal_terbayar ?? 0)), 0, ',', '.'),
            '{{ payment_date }}'      => $payment->tanggal_bayar?->translatedFormat('d F Y') ?? '-',
            '{{ nomor_transaksi }}'   => $payment->nomor_transaksi,
            '{{ metode_bayar }}'      => $payment->metode_bayar_label,
            '{{ periode }}'           => $bill?->periode ?? '-',
            '{{ petugas }}'           => $payment->petugas?->name ?? 'Sistem',
            '{{ keterangan }}'        => $payment->keterangan ?? '-',
            '{{ print_date }}'        => now('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB',
        ];

        return str_replace(array_keys($map), array_values($map), $text);
    }

    /**
     * Generate PDF struk dan kembalikan instance PDF.
     */
    public function generatePdf(Payment $payment, ?ReceiptTemplate $template = null): \Barryvdh\DomPDF\PDF
    {
        $payment->load(['bill.paymentType', 'bill.institution', 'student.class', 'petugas', 'institution']);

        // Jika tidak ada template, gunakan default institution
        if (! $template) {
            $template = ReceiptTemplate::where('institution_id', $payment->institution_id)
                ->where('is_default', true)
                ->first();
        }

        $ukuran      = $template?->ukuran ?? 'a4';
        $headerHtml  = $template ? $this->resolvePlaceholders($template->header ?? '', $payment) : '';
        $footerHtml  = $template ? $this->resolvePlaceholders($template->footer ?? '', $payment) : '';

        // Dimensi kertas
        $paperConfig = match ($ukuran) {
            'thermal58' => ['width' => 57, 'height' => 200],
            'thermal80' => ['width' => 79, 'height' => 200],
            default     => 'a4',
        };

        $html = View::make('pdf.struk', [
            'payment'    => $payment,
            'template'   => $template,
            'headerHtml' => $headerHtml,
            'footerHtml' => $footerHtml,
            'ukuran'     => $ukuran,
        ])->render();

        $pdf = Pdf::loadHTML($html);

        if (is_array($paperConfig)) {
            $pdf->setPaper([$paperConfig['width'] * 0.03937, 0, $paperConfig['width'] * 0.03937, $paperConfig['height'] * 0.03937], 'portrait');
        } else {
            $pdf->setPaper($paperConfig, 'portrait');
        }

        return $pdf;
    }

    /**
     * Resolve placeholder untuk preview (tanpa payment object).
     */
    public function previewTemplate(string $text): string
    {
        $map = [
            '{{ school_name }}'     => 'SMA Contoh Bangsa',
            '{{ student_name }}'    => 'Budi Santoso',
            '{{ student_nis }}'     => '2024001',
            '{{ student_class }}'   => 'X RPL 1',
            '{{ payment_type }}'    => 'SPP Bulanan',
            '{{ amount }}'          => 'Rp 250.000',
            '{{ total_amount }}'    => 'Rp 250.000',
            '{{ amount_paid }}'     => 'Rp 250.000',
            '{{ remaining }}'       => 'Rp 0',
            '{{ payment_date }}'    => now('Asia/Jakarta')->translatedFormat('d F Y'),
            '{{ nomor_transaksi }}' => 'SMK/TRX/2024/00001',
            '{{ metode_bayar }}'    => 'Tunai',
            '{{ periode }}'         => 'Juli 2024',
            '{{ petugas }}'         => 'Admin Sekolah',
            '{{ keterangan }}'      => '-',
            '{{ print_date }}'      => now('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB',
        ];
        return str_replace(array_keys($map), array_values($map), $text);
    }
}
