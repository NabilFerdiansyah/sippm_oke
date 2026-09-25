<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Akun baru (dibuat oleh Manager) atau akun yang baru saja direset kata
 * sandinya wajib mengganti kata sandi sementara sebelum mengakses menu lain.
 */
class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password && ! $request->routeIs('profil.*') && ! $request->routeIs('logout')) {
            return redirect()->route('profil.edit')
                ->with('warning', 'Demi keamanan, silakan buat kata sandi baru sebelum melanjutkan.');
        }

        return $next($request);
    }
}
