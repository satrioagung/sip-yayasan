<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * Pastikan pengguna hanya mengakses resource dari institusi mereka sendiri.
     * Super Admin dibebaskan dari pembatasan ini.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Super Admin boleh akses semua institusi
        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        // Ambil institution_id dari route parameter atau request
        $resourceInstitutionId = $request->route('institution_id')
            ?? $request->input('institution_id')
            ?? $request->route('institution')?->id;

        if ($resourceInstitutionId && (int) $resourceInstitutionId !== (int) $user->institution_id) {
            abort(403, 'Anda tidak memiliki akses ke institusi tersebut.');
        }

        return $next($request);
    }
}
