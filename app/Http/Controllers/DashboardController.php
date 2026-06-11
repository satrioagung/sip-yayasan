<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'user'        => $user,
            'institution' => $user->institution,
            'roleName'    => $user->getRoleNames()->first() ?? 'Pengguna',
        ]);
    }
}
