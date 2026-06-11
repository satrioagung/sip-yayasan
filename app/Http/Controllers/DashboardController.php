<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user        = $request->user();
        $institution = $user->institution; // null untuk Super Admin

        // Statistik — akan diisi modul berikutnya
        $statistik = [
            'total_siswa'      => 0,
            'total_tagihan'    => 0,
            'total_pembayaran' => 0,
            'saldo_kas'        => 0,
            'total_tunggakan'  => 0,
        ];

        // Tambahan data untuk Super Admin
        $totalLembaga = null;
        if ($user->hasRole('Super Admin')) {
            $totalLembaga = Institution::count();
        }

        return view('dashboard', [
            'user'         => $user,
            'institution'  => $institution,
            'roleName'     => $user->getRoleNames()->first() ?? 'Pengguna',
            'statistik'    => $statistik,
            'totalLembaga' => $totalLembaga,
        ]);
    }
}
