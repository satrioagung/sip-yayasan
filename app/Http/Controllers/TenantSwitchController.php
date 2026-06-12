<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class TenantSwitchController extends Controller
{
    /**
     * Halaman pilih lembaga untuk Super Admin.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if (! $request->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $lembaga = Institution::aktif()
            ->orderBy('name')
            ->get();

        $activeTenantId = $request->session()->get('active_tenant_id');

        return view('tenant.switch', compact('lembaga', 'activeTenantId'));
    }

    /**
     * Set lembaga aktif di session Super Admin.
     */
    public function switch(Request $request, string $id): RedirectResponse
    {
        if (! $request->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $institution = Institution::aktif()->findOrFail((int) $id);
        $request->session()->put('active_tenant_id', $institution->id);

        // Set config juga langsung
        Config::set('tenant.id', $institution->id);

        $redirect = $request->input('redirect', route('dashboard'));

        return redirect($redirect)
            ->with('success', "Berpindah ke lembaga: {$institution->nama_lengkap}");
    }

    /**
     * Hapus tenant aktif (kembali ke mode Super Admin global).
     */
    public function clear(Request $request): RedirectResponse
    {
        if (! $request->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $request->session()->forget('active_tenant_id');

        return redirect()->route('dashboard')
            ->with('success', 'Mode lembaga dihapus. Anda kembali ke tampilan global Super Admin.');
    }
}
