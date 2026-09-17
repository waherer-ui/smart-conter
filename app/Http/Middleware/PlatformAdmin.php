<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlatformAdmin
{
    /**
     * Memastikan hanya Admin Platform KasirKU
     * yang dapat mengakses area platform.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = session('user_id');

        $user = $userId
            ? User::find($userId)
            : null;

        if (!$user || !$user->is_platform_admin) {
    return redirect()->route('dashboard');
}

        return $next($request);
    }
}