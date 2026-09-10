<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        return view('admin.users.index', compact('users'));
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
        | Buat User
        |--------------------------------------------------------------------------
        */

        $user = User::create([
    'name' => trim($request->name),

    'email' => strtolower(trim($request->email)),

    'password' => Hash::make($request->password),

    'role' => $request->role,
      ]);
      
      $user->stores()->attach(
          $this->activeStoreId(),
          [
              'role' => $request->role,
          ]
      );


        /*
        |--------------------------------------------------------------------------
        | Kembali ke Halaman Admin
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.index')
            ->with(
                'success',
                'Akun ' . ucfirst($request->role) . ' berhasil ditambahkan!'
            );
    }


    /**
     * Menghapus akun.
     */
    public function destroy($id)
    {
        /*
        |--------------------------------------------------------------------------
        | Cari User
        |--------------------------------------------------------------------------
        */

        $storeId = $this->activeStoreId();

          $user = User::whereHas('stores', function ($query) use ($storeId) {
              $query->where('stores.id', $storeId);
          })
          ->where('id', $id)
          ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Jangan Izinkan Admin Menghapus Akunnya Sendiri
        |--------------------------------------------------------------------------
        */

        if ((int) $user->id === (int) session('user_id')) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Anda tidak dapat menghapus akun yang sedang digunakan.'
                );
        }


        /*
|--------------------------------------------------------------------------
| Keluarkan User dari Toko Aktif
|--------------------------------------------------------------------------
*/

        $userName = $user->name;

          $user->stores()->detach($storeId);


        /*
        |--------------------------------------------------------------------------
        | Kembali
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.index')
            ->with(
                'success',
                'Akun "' . $userName . '" berhasil dihapus.'
            );
    }
}