<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $username = trim(
            $request->input('username')
        );

        $password = $request->input('password');

        $user = User::where('email', $username)
            ->orWhere('name', $username)
            ->first();

        if (
            !$user ||
            !Hash::check(
                $password,
                $user->password
            )
        ) {
            return back()
                ->withInput(
                    $request->only('username')
                )
                ->with(
                    'error',
                    'Username atau password salah, atau akun tidak terdaftar.'
                );
        }

        if (
            !in_array(
                $user->role,
                ['admin', 'kasir']
            )
        ) {
            return back()
                ->withInput(
                    $request->only('username')
                )
                ->with(
                    'error',
                    'Role akun tidak dikenali.'
                );
        }

        /*
         * =====================================================
         * SIMPAN DATA LAMA UNTUK AUDIT
         * =====================================================
         *
         * Belum ada active_store sebelum session dibuat,
         * jadi store_id akan diisi setelah proses login jika
         * memang ada toko aktif.
         */

        $request->session()->regenerate();

        $request->session()->put([
            'logged_in' => true,
            'user_id' => $user->id,
            'username' => $user->name,
            'user_role' => $user->role,
        ]);

        /*
         * =====================================================
         * TENTUKAN REDIRECT
         * =====================================================
         */

        if ($user->role === 'admin') {

            /*
             * Audit login.
             */
            AuditLogService::log(
                'login',
                'Login berhasil untuk akun "' .
                $user->name .
                '".',
                $user,
                $user->id,
                session('active_store_id'),
                null,
                [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            );

            return redirect()
                ->intended(
                    route('dashboard')
                )
                ->with(
                    'success',
                    'Selamat datang, Administrator!'
                );
        }

        if ($user->role === 'kasir') {

            /*
             * Audit login.
             */
            AuditLogService::log(
                'login',
                'Login berhasil untuk akun "' .
                $user->name .
                '".',
                $user,
                $user->id,
                session('active_store_id'),
                null,
                [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            );

            return redirect()
                ->intended(
                    route('kasir.index')
                )
                ->with(
                    'success',
                    'Selamat datang, ' .
                    $user->name .
                    '!'
                );
        }

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
    | PROFIL: MENAMPILKAN HALAMAN EDIT PROFIL
    |--------------------------------------------------------------------------
    */

    public function edit()
    {
        /*
         * Ambil data user berdasarkan session ID
         * yang sedang aktif.
         */
        $user = User::findOrFail(
            session('user_id')
        );

        /*
         * Ambil kode referral milik owner.
         */
        $referral = $user->referral;

        /*
         * Buat kode referral otomatis jika belum punya.
         */
        if (!$referral) {

            do {

                $code =
                    'KASIR' .
                    strtoupper(
                        Str::random(6)
                    );

            } while (
                \App\Models\Referral::where(
                    'code',
                    $code
                )->exists()
            );

            $referral =
                \App\Models\Referral::create([
                    'owner_id' =>
                        $user->id,

                    'code' =>
                        $code,

                    'is_active' =>
                        true,
                ]);
        }

        return view(
            'profile',
            compact(
                'user',
                'referral'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PROFIL: MEMPROSES UPDATE PROFIL
    | NAMA, PASSWORD, AVATAR
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request
    ) {
        $user = User::findOrFail(
            session('user_id')
        );

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
        ]);

        /*
         * =====================================================
         * SIMPAN DATA LAMA
         * =====================================================
         */

        $oldValues = [
            'name' =>
                $user->name,

            'email' =>
                $user->email,

            'avatar' =>
                $user->avatar,
        ];

        /*
         * Penanda perubahan.
         */
        $changes = [];

        /*
         * =====================================================
         * UPDATE NAMA
         * =====================================================
         */

        $newName =
            trim(
                $request->input('name')
            );

        if (
            $user->name !==
            $newName
        ) {

            $changes[] =
                'Nama: "' .
                $user->name .
                '" → "' .
                $newName .
                '"';

            $user->name =
                $newName;

            /*
             * Update session username
             * supaya navbar langsung berubah.
             */
            session([
                'username' =>
                    $user->name,
            ]);
        }

        /*
         * =====================================================
         * UPDATE PASSWORD
         * =====================================================
         */

        if (
            $request->filled(
                'password'
            )
        ) {

            $user->password =
                Hash::make(
                    $request->input(
                        'password'
                    )
                );

            $changes[] =
                'Password diperbarui';
        }

        /*
         * =====================================================
         * UPDATE AVATAR
         * =====================================================
         */

        if (
            $request->hasFile(
                'avatar'
            )
        ) {

            /*
             * Hapus avatar lama dari R2.
             */
            if ($user->avatar) {

                Storage::disk(
                    's3'
                )->delete(
                    $user->avatar
                );
            }

            $file =
                $request->file(
                    'avatar'
                );

            $filename =
                time() .
                '_' .
                uniqid() .
                '.' .
                $file->getClientOriginalExtension();

            /*
             * Upload avatar baru ke R2.
             */
            Storage::disk(
                's3'
            )->putFileAs(
                'avatars',
                $file,
                $filename
            );

            /*
             * Simpan path R2 ke database.
             */
            $user->avatar =
                'avatars/' .
                $filename;

            $changes[] =
                'Foto profil diperbarui';
        }

        /*
         * =====================================================
         * SIMPAN USER
         * =====================================================
         */

        $user->save();

        /*
         * =====================================================
         * AUDIT LOG PROFIL
         * =====================================================
         *
         * Hanya dibuat jika memang ada perubahan.
         */

        if (!empty($changes)) {

            AuditLogService::log(
                'profile_updated',

                'Mengubah profil akun "' .
                $user->name .
                '": ' .
                implode(
                    ' ; ',
                    $changes
                ),

                $user,

                $user->id,

                session(
                    'active_store_id'
                ),

                $oldValues,

                [
                    'name' =>
                        $user->name,

                    'email' =>
                        $user->email,

                    'avatar' =>
                        $user->avatar,

                    'password' =>
                        in_array(
                            'Password diperbarui',
                            $changes,
                            true
                        )
                            ? '[DIUBAH]'
                            : '[TIDAK DIUBAH]',
                ]
            );
        }

        return redirect()
            ->route('profil')
            ->with(
                'success',
                'Profil berhasil diperbarui!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(
        Request $request
    ) {
        /*
         * Simpan informasi user sebelum
         * session dihancurkan.
         */
        $userId =
            session('user_id');

        $user =
            $userId
                ? User::find($userId)
                : null;

        $storeId =
            session(
                'active_store_id'
            );

        /*
         * =====================================================
         * AUDIT LOG LOGOUT
         * =====================================================
         *
         * Harus dilakukan SEBELUM session invalidate,
         * karena AuditLogService membaca session user_id.
         */

        if ($user) {

            AuditLogService::log(
                'logout',

                'Logout dari akun "' .
                $user->name .
                '".',

                $user,

                $user->id,

                $storeId,

                null,

                [
                    'user_id' =>
                        $user->id,

                    'name' =>
                        $user->name,

                    'email' =>
                        $user->email,

                    'role' =>
                        $user->role,
                ]
            );
        }

        /*
         * =====================================================
         * HANCURKAN SESSION
         * =====================================================
         */

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Anda berhasil logout.'
            );
    }
}