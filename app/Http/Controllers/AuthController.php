<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MENAMPILKAN HALAMAN LOGIN
    |--------------------------------------------------------------------------
    */

    public function showLoginForm()
    {
        // Jika sudah login, arahkan sesuai role
        if (session('logged_in')) {

            if (session('user_role') === 'admin') {
                return redirect()->route('dashboard');
            }

            if (session('user_role') === 'kasir') {
                return redirect()->route('kasir.index');
            }
        }

        return view('login');
    }


    /*
    |--------------------------------------------------------------------------
    | PROSES LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'username' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Ambil Input
        |--------------------------------------------------------------------------
        */

        $username = trim($request->input('username'));
        $password = $request->input('password');


        /*
        |--------------------------------------------------------------------------
        | Cari User
        |--------------------------------------------------------------------------
        |
        | Bisa menggunakan:
        | - Email
        | - Nama
        |
        */

        $user = User::where('email', $username)
            ->orWhere('name', $username)
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Cek User dan Password
        |--------------------------------------------------------------------------
        */

        if (!$user || !Hash::check($password, $user->password)) {

            return back()
                ->withInput($request->only('username'))
                ->with(
                    'error',
                    'Username atau password salah, atau akun tidak terdaftar.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Cek Role
        |--------------------------------------------------------------------------
        */

        if (!in_array($user->role, ['admin', 'kasir'])) {

            return back()
                ->withInput($request->only('username'))
                ->with(
                    'error',
                    'Role akun tidak dikenali.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Regenerasi Session
        |--------------------------------------------------------------------------
        |
        | Mencegah Session Fixation.
        |
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Simpan Informasi Login
        |--------------------------------------------------------------------------
        */

        $request->session()->put([
            'logged_in' => true,

            'user_id' => $user->id,

            'username' => $user->name,

            'user_role' => $user->role,
        ]);


        /*
        |--------------------------------------------------------------------------
        | REDIRECT ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'admin') {

            return redirect()
                ->intended(route('dashboard'))
                ->with(
                    'success',
                    'Selamat datang, Administrator!'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECT KASIR
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'kasir') {

            return redirect()
                ->intended(route('kasir.index'))
                ->with(
                    'success',
                    'Selamat datang, ' . $user->name . '!'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Fallback
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'error',
                'Role akun tidak dikenali.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Hapus Session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();


        /*
        |--------------------------------------------------------------------------
        | Regenerasi CSRF Token
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerateToken();


        /*
        |--------------------------------------------------------------------------
        | Kembali ke Login
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Anda berhasil logout.'
            );
    }
}