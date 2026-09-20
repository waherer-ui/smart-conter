<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private function activeStoreId(): int
    {
        return (int) session('active_store_id');
    }

    /**
     * Menampilkan halaman manajemen user.
     */
    public function index()
    {
        $storeId = $this->activeStoreId();

        $users = User::whereHas('stores', function ($query) use ($storeId) {
            $query->where('stores.id', $storeId);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return view(
            'admin.users.index',
            compact('users')
        );
    }

    /**
     * Membuat akun Admin atau Kasir.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:users,name',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'role' => [
                'required',
                'in:admin,kasir',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | STORE AKTIF
        |--------------------------------------------------------------------------
        */

        $store = Store::find(
            $this->activeStoreId()
        );

        if (!$store || !$store->canAddStaff()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Batas jumlah akun pada paket Anda sudah tercapai. Silakan upgrade paket untuk menambah akun baru.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | BUAT USER
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name' => trim(
                $request->name
            ),

            'email' => strtolower(
                trim($request->email)
            ),

            'password' => Hash::make(
                $request->password
            ),

            'role' => $request->role,
        ]);

        /*
        |--------------------------------------------------------------------------
        | HUBUNGKAN USER DENGAN STORE
        |--------------------------------------------------------------------------
        */

        $user->stores()->attach(
            $store->id,
            [
                'role' => $request->role,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG - STAFF DIBUAT
        |--------------------------------------------------------------------------
        */

        AuditLogService::log(
            'staff_created',
            'Menambahkan akun ' .
            ucfirst($request->role) .
            ' "' .
            $user->name .
            '" ke toko "' .
            $store->name .
            '".',
            $user,
            null,
            $store->id,
            null,
            [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $request->role,
                'store_id' => $store->id,
                'store_name' => $store->name,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | KEMBALI KE HALAMAN ADMIN
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.index')
            ->with(
                'success',
                'Akun ' .
                ucfirst($request->role) .
                ' berhasil ditambahkan!'
            );
    }

    /**
     * Menghapus / mengeluarkan akun dari toko aktif.
     */
    public function destroy($id)
    {
        /*
        |--------------------------------------------------------------------------
        | STORE AKTIF
        |--------------------------------------------------------------------------
        */

        $storeId = $this->activeStoreId();

        $store = Store::findOrFail(
            $storeId
        );

        /*
        |--------------------------------------------------------------------------
        | CARI USER DI STORE AKTIF
        |--------------------------------------------------------------------------
        */

        $user = User::whereHas(
            'stores',
            function ($query) use ($storeId) {

                $query->where(
                    'stores.id',
                    $storeId
                );
            }
        )
        ->where(
            'id',
            $id
        )
        ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | JANGAN IZINKAN ADMIN MENGHAPUS AKUN SENDIRI
        |--------------------------------------------------------------------------
        */

        if (
            (int) $user->id ===
            (int) session('user_id')
        ) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Anda tidak dapat menghapus akun yang sedang digunakan.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA SEBELUM DETACH
        |--------------------------------------------------------------------------
        */

        $userName = $user->name;
        $userEmail = $user->email;

        $pivot = $user->stores()
            ->where(
                'stores.id',
                $storeId
            )
            ->first();

        $storeRole = $pivot?->pivot?->role
            ?? $user->role;

        /*
        |--------------------------------------------------------------------------
        | KELUARKAN USER DARI TOKO AKTIF
        |--------------------------------------------------------------------------
        */

        $user->stores()->detach(
            $storeId
        );

        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG - STAFF DIKELUARKAN
        |--------------------------------------------------------------------------
        */

        AuditLogService::log(
            'staff_deleted',
            'Mengeluarkan akun ' .
            ucfirst($storeRole) .
            ' "' .
            $userName .
            '" dari toko "' .
            $store->name .
            '".',
            $user,
            null,
            $store->id,
            [
                'name' => $userName,
                'email' => $userEmail,
                'role' => $storeRole,
                'store_id' => $store->id,
                'store_name' => $store->name,
            ],
            null
        );

        /*
        |--------------------------------------------------------------------------
        | KEMBALI
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.index')
            ->with(
                'success',
                'Akun "' .
                $userName .
                '" berhasil dikeluarkan dari toko.'
            );
    }
}