<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user ke Google.
     */
    public function redirect(Request $request)
    {
        $mode = $request->query('mode', 'login');

        if (!in_array($mode, ['login', 'register'], true)) {
            $mode = 'login';
        }

        session(['google_auth_mode' => $mode]);

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Callback dari Google.
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();
        $name = $googleUser->getName() ?: 'Pengguna Google';

        if (!$googleId || !$email) {
            return redirect()->route('login')
                ->with('error', 'Data akun Google tidak lengkap.');
        }

        $mode = session()->pull('google_auth_mode', 'login');

        /*
         * MODE REGISTER
         *
         * Google hanya mengisi identitas.
         * Data toko akan dilengkapi melalui form register.
         */
        if ($mode === 'register') {
            $existingUser = User::where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();

            if ($existingUser) {
                return redirect()->route('login')
                    ->with('error', 'Email Google tersebut sudah terdaftar. Silakan login.');
            }

            session([
                'google_register' => [
                    'google_id' => $googleId,
                    'name' => $name,
                    'email' => $email,
                ],
            ]);

            return redirect()->route('register')
                ->with('success', 'Akun Google berhasil terhubung. Lengkapi data toko Anda.');
        }

        /*
         * MODE LOGIN
         *
         * Cari berdasarkan google_id terlebih dahulu,
         * kemudian email.
         */
        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        /*
         * Jika belum punya akun Kasir½M,
         * arahkan ke halaman daftar.
         */
        if (!$user) {
            session([
                'google_register' => [
                    'google_id' => $googleId,
                    'name' => $name,
                    'email' => $email,
                ],
            ]);

            return redirect()->route('register')
                ->with('success', 'Akun Google belum terdaftar. Lengkapi data toko untuk membuat akun Kasir½M.');
        }

        /*
         * Hubungkan Google ID jika akun sebelumnya
         * dibuat menggunakan email/password.
         */
        if (!$user->google_id) {
            $user->google_id = $googleId;
            $user->save();
        }

        return $this->loginUser($request, $user);
    }

    /**
     * Membuat session login Kasir½M.
     */
    private function loginUser(Request $request, User $user)
    {
        $request->session()->regenerate();

        if ($user->is_platform_admin) {
            $request->session()->put([
                'logged_in' => true,
                'user_id' => $user->id,
                'username' => $user->name,
                'user_role' => $user->role,
                'is_platform_admin' => true,
            ]);

            return redirect()->route('admin-kasirku.dashboard');
        }

        $store = $user->stores()
            ->orderBy('stores.id')
            ->first();

        if (!$store) {
            return redirect()->route('register')
                ->with('error', 'Akun Anda belum memiliki toko.');
        }

        $request->session()->put([
            'logged_in' => true,
            'user_id' => $user->id,
            'username' => $user->name,
            'user_role' => $user->role,
            'active_store_id' => $store->id,
            'is_platform_admin' => false,
        ]);

        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }

        return redirect()->route('kasir.index');
    }
}
