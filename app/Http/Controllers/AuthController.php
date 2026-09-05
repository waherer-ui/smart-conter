<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

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
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = trim($request->input('username'));
        $password = $request->input('password');

        $user = User::where('email', $username)
            ->orWhere('name', $username)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username atau password salah, atau akun tidak terdaftar.');
        }

        if (!in_array($user->role, ['admin', 'kasir'])) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Role akun tidak dikenali.');
        }

        $request->session()->regenerate();

        $request->session()->put([
            'logged_in' => true,
            'user_id' => $user->id,
            'username' => $user->name,
            'user_role' => $user->role,
        ]);

        if ($user->role === 'admin') {
            return redirect()
                ->intended(route('dashboard'))
                ->with('success', 'Selamat datang, Administrator!');
        }

        if ($user->role === 'kasir') {
            return redirect()
                ->intended(route('kasir.index'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('error', 'Role akun tidak dikenali.');
    }


    /*
    |--------------------------------------------------------------------------
    | PROFIL: MENAMPILKAN HALAMAN EDIT PROFIL
    |--------------------------------------------------------------------------
    */

    public function edit()
    {
        // Ambil data user berdasarkan session ID yang sedang aktif
        $user = User::findOrFail(session('user_id'));

        return view('profile', compact('user'));
    }


    /*
    |--------------------------------------------------------------------------
    | PROFIL: MEMPROSES UPDATE PROFIL (NAMA, PASSWORD, AVATAR)
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {
        $user = User::findOrFail(session('user_id'));

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // Update Nama
        $user->name = $request->input('name');
        
        // Update Session Username supaya langsung berubah di navbar
        session(['username' => $user->name]);

        // Update Password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Update Foto Avatar jika ada yang di-upload
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->avatar && File::exists(public_path('avatars/' . $user->avatar))) {
                File::delete(public_path('avatars/' . $user->avatar));
            }

            $file = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Pindahkan file ke folder public/avatars
            $file->move(public_path('avatars'), $filename);
            
            $user->avatar = $filename;
        }

        $user->save();

        return redirect()
            ->route('profil')
            ->with('success', 'Profil berhasil diperbarui!');
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Anda berhasil logout.');
    }
}
