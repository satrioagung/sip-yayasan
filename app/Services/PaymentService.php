<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Institution;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    /**
     * Buat pembayaran dan update status tagihan secara atomik.
     */
    public function pay(Bill $bill, array $data, ?UploadedFile $bukti = null): Payment
    {
        return DB::transaction(function () use ($bill, $data, $bukti) {
            // Upload bukti jika ada
            $buktiPath = null;
            if ($bukti) {
                $buktiPath = $bukti->store("bukti-pembayaran/{$bill->institution_id}", 'public');
            }

            // Generate nomor transaksi
            $nomorTrx = $this->generateNomorTransaksi($bill->institution_id);

            // Simpan pembayaran
            $payment = Payment::create([
                'institution_id'  => $bill->institution_id,
                'bill_id'         => $bill->id,
                'student_id'      => $bill->student_id,
                'petugas_id'      => $data['petugas_id'] ?? null,
                'nomor_transaksi' => $nomorTrx,
                'tanggal_bayar'   => Carbon::createFromFormat('d/m/Y', $data['tanggal_bayar']),
                'metode_bayar'    => $data['metode_bayar'],
                'nominal_bayar'   => (int) $data['nominal_bayar'],
                'bukti_file'      => $buktiPath,
                'keterangan'      => $data['keterangan'] ?? null,
            ]);

            // Update tagihan
            $totalTerbayar = $bill->nominal_terbayar + $payment->nominal_bayar;
            $totalTerbayar = min($totalTerbayar, $bill->nominal); // jangan melebihi nominal

            $status = 'belum_bayar';
            if ($totalTerbayar >= $bill->nominal) {
                $status = 'lunas';
            } elseif ($totalTerbayar > 0) {
                $status = 'sebagian';
            }

            $bill->update([
                'nominal_terbayar' => $totalTerbayar,
                'status'           => $status,
            ]);

            return $payment->load(['bill.paymentType', 'student', 'petugas', 'bill.institution']);
        });
    }

    /**
     * Hapus pembayaran dan rollback status tagihan.
     */
    public function cancel(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $bill = $payment->bill;

            // Kurangi nominal terbayar
            $totalTerbayar = max(0, $bill->nominal_terbayar - $payment->nominal_bayar);

            $status = 'belum_bayar';
            if ($totalTerbayar >= $bill->nominal) {
                $status = 'lunas';
            } elseif ($totalTerbayar > 0) {
                $status = 'sebagian';
            }

            $bill->update([
                'nominal_terbayar' => $totalTerbayar,
                'status'           => $status,
            ]);

            // Hapus file bukti
            if ($payment->bukti_file) {
                Storage::disk('public')->delete($payment->bukti_file);
            }

            $payment->delete();
        });
    }

    /**
     * Generate nomor transaksi: [PREFIX]/TRX/[TAHUN]/[5-digit-urut]
     */
    public function generateNomorTransaksi(int $institutionId): string
    {
        $institution = Institution::find($institutionId);
        $prefix      = strtoupper($institution?->code ?? 'SPP');
        $tahun       = now()->format('Y');

        // Ambil nomor urut terakhir dari format nomor transaksi yang ada
        // misal: SMK/TRX/2026/00003 → ambil 3, berikutnya 4
        $lastNomor = Payment::where('institution_id', $institutionId)
            ->whereYear('created_at', $tahun)
            ->whereRaw("nomor_transaksi LIKE ?", ["{$prefix}/TRX/{$tahun}/%"])
            ->selectRaw("COALESCE(MAX(CAST(SPLIT_PART(nomor_transaksi, '/', 4) AS INTEGER)), 0) as last_urut")
            ->value('last_urut') ?? 0;

        $urut = str_pad((int) $lastNomor + 1, 5, '0', STR_PAD_LEFT);

        return "{$prefix}/TRX/{$tahun}/{$urut}";
    }
}
