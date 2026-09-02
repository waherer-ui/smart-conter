<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(
        Request $request,
        Closure $next,
        string $role = ''
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | CEK LOGIN
        |--------------------------------------------------------------------------
        */

        if (
            !session('logged_in') ||
            !session('user_role')
        ) {

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Silakan login terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL ROLE USER
        |--------------------------------------------------------------------------
        */

        $userRole = session('user_role');


        /*
        |--------------------------------------------------------------------------
        | CEK ROLE KHUSUS
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | ->middleware('auth.role:admin')
        |
        | Hanya Admin yang boleh masuk.
        |
        */

        if ($role !== '' && $userRole !== $role) {

            /*
            |--------------------------------------------------------------------------
            | Jika Kasir mencoba akses halaman Admin
            |--------------------------------------------------------------------------
            */

            if ($userRole === 'kasir') {

                return redirect()
                    ->route('kasir.index')
                    ->with(
                        'error',
                        'Akses ditolak. Halaman ini khusus Administrator.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Jika role tidak dikenali
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Anda tidak memiliki hak akses ke halaman ini.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | LANJUTKAN REQUEST
        |--------------------------------------------------------------------------
        */

        return $next($request);
    }
}