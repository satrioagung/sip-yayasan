<?php

namespace App\Http\Middleware;

use App\Models\Institution;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetActiveTenant
{
    /**
     * Tentukan lembaga aktif berdasarkan user yang login.
     * Super Admin bisa switch lembaga via session/parameter.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Super Admin: bisa switch lembaga via parameter atau session
        if ($user->hasRole('Super Admin')) {
            $institutionId = $request->query('switch_tenant')
                ?? $request->session()->get('active_tenant_id');

            if ($institutionId) {
                $institution = Institution::aktif()->find($institutionId);
                if ($institution) {
                    $request->session()->put('active_tenant_id', $institution->id);
                    $this->setTenantConfig($institution);

                    // Redirect bersih setelah switch (hapus query string)
                    if ($request->query('switch_tenant')) {
                        return redirect($request->url());
                    }
                }
            }

            // Share ke semua views
            view()->share('activeTenant', isset($institution) ? $institution : null);
            return $next($request);
        }

        // Role lain: lembaga diambil dari profil user
        if ($user->institution_id) {
            $institution = Institution::find($user->institution_id);

            if ($institution && $institution->is_active) {
                $this->setTenantConfig($institution);
                view()->share('activeTenant', $institution);
            } else {
                // Lembaga nonaktif — paksa logout
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Anda tidak dapat masuk karena lembaga sedang nonaktif.']);
            }
        } else {
            view()->share('activeTenant', null);
        }

        return $next($request);
    }

    /**
     * Set konfigurasi tenant ke runtime config Laravel.
     */
    private function setTenantConfig(Institution $institution): void
    {
        Config::set('tenant.id', $institution->id);
        Config::set('tenant.name', $institution->name);
        Config::set('tenant.kode', $institution->code);
        Config::set('tenant.warna_tema', $institution->warna_tema ?? '#2563eb');
        Config::set('tenant.prefix_struk', $institution->prefix_nomor_struk ?? 'SPP');
        Config::set('tenant.footer_struk', $institution->footer_struk ?? '');
    }
}
