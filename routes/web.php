<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\AuthController;



/*
|--------------------------------------------------------------------------
| LOGIN - PUBLIK
|--------------------------------------------------------------------------
*/

Route::get('/produk-image/{path}', function ($path) {

    $path = urldecode($path);

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return response()->file(
        Storage::disk('public')->path($path)
    );

})->where('path', '.*')->name('produk.image');


// Halaman Login
Route::get('/login', function () {

    if (session('logged_in')) {

        if (session('user_role') === 'admin') {
            return redirect()->route('dashboard');
        }

        if (session('user_role') === 'kasir') {
            return redirect()->route('kasir.index');
        }
    }

    return view('login');

})->name('login');


/*
|--------------------------------------------------------------------------
| PROSES LOGIN
|--------------------------------------------------------------------------
*/

Route::post('/proses-login', function (Request $request) {

    $username = trim($request->input('username'));
    $password = $request->input('password');


    /*
    |--------------------------------------------------------------------------
    | Validasi Input
    |--------------------------------------------------------------------------
    */

    if (empty($username) || empty($password)) {

        return back()
            ->withInput($request->only('username'))
            ->with(
                'error',
                'Username/Email dan password wajib diisi!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Cari User
    |--------------------------------------------------------------------------
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
                'Username atau password salah, atau akun tidak terdaftar!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Regenerasi Session
    |--------------------------------------------------------------------------
    */

    $request->session()->regenerate();


    /*
    |--------------------------------------------------------------------------
    | Simpan Data User
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
    | REDIRECT BERDASARKAN ROLE
    |--------------------------------------------------------------------------
    */

    // ADMIN
    if ($user->role === 'admin') {

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Selamat datang, Administrator!'
            );
    }


    // KASIR
    if ($user->role === 'kasir') {

        return redirect()
            ->route('kasir.index')
            ->with(
                'success',
                'Selamat datang, ' . $user->name . '!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ROLE TIDAK DIKENALI
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

})->name('proses.login');


/*
|--------------------------------------------------------------------------
| ADMIN + KASIR
| WAJIB LOGIN
|--------------------------------------------------------------------------
|
| Fitur yang boleh digunakan Admin dan Kasir:
|
| - Dashboard
| - Produk
| - Kasir
| - Transaksi
| - Riwayat
| - Melihat Pengeluaran
| - Menambah Pengeluaran
| - Melihat Laporan
|
|--------------------------------------------------------------------------
*/

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [ProductController::class, 'dashboard']
    )->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | PRODUK
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/produk',
        [ProductController::class, 'index']
    )->name('produk.index');


    // Tambah Produk / Restock
    Route::post(
        '/produk',
        [ProductController::class, 'store']
    )->middleware('auth.role')
    ->name('produk.store');

    Route::get('/produk/scan/{sku}', [ProductController::class, 'scanBySku'])
    ->name('produk.scan');
    
    // Edit Produk
    Route::put(
        '/produk/{id}',
        [ProductController::class, 'update']
    )->middleware('auth.role')
    ->name('produk.update');


    // Hapus Produk
    Route::delete(
        '/produk/{id}',
        [ProductController::class, 'destroy']
    )->middleware('auth.role:admin')
    ->name('produk.destroy');


    // Hapus Riwayat Produk
    Route::delete(
        '/produk/history/{id}',
        [ProductController::class, 'destroyHistory']
    )->middleware('auth.role:admin')
    ->name('produk.history.destroy');


    /*
    |--------------------------------------------------------------------------
    | KASIR / POS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/kasir',
        [ProductController::class, 'kasir']
    )->name('kasir.index');


    // Simpan transaksi
    Route::post(
        '/transaksi',
        [TransactionController::class, 'store']
    )->middleware('auth.role')
    ->name('transaksi.store');


    /*
    |--------------------------------------------------------------------------
    | RIWAYAT TRANSAKSI
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/riwayat',
        [TransactionController::class, 'index']
    )->name('riwayat');


    /*
    |--------------------------------------------------------------------------
    | PENGELUARAN
    |--------------------------------------------------------------------------
    |
    | Admin + Kasir boleh:
    |
    | - Melihat daftar pengeluaran
    | - Menambah pengeluaran
    |
    */

    Route::get(
        '/pengeluaran',
        [ExpenseController::class, 'index']
    )->name('pengeluaran');


    Route::post(
        '/pengeluaran',
        [ExpenseController::class, 'store']
    )->name('pengeluaran.store');


    /*
    |--------------------------------------------------------------------------
    | LAPORAN
    |--------------------------------------------------------------------------
    |
    | Admin + Kasir boleh melihat laporan.
    |
    | Data HPP dan laba bersih akan dibatasi
    | berdasarkan role di LaporanController.
    |
    */

    Route::get(
        '/laporan',
        [LaporanController::class, 'index']
    )->name('laporan');
    

Route::middleware('auth.role')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PROFIL
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profil',
        [AuthController::class, 'edit']
    )->name('profil');

    Route::put(
        '/profil',
        [AuthController::class, 'update']
    )->name('profile.update');
    
        /*
      |--------------------------------------------------------------------------
      | CETAK LABEL QR
      |--------------------------------------------------------------------------
      */
      
      Route::get(
          '/cetak-label',
          [LabelController::class, 'index']
      )->name('cetaklabel');
      });

/*
|--------------------------------------------------------------------------
| ADMIN SAJA
|--------------------------------------------------------------------------
|
| Hanya Admin yang boleh:
|
| - Manajemen User
| - Setting
| - Edit Pengeluaran
| - Hapus Pengeluaran
|
|--------------------------------------------------------------------------
*/

Route::middleware('auth.role:admin')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | MANAJEMEN USER
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin',
        [UserController::class, 'index']
    )->name('admin.index');


    Route::post(
        '/admin/users',
        [UserController::class, 'store']
    )->name('admin.users.store');


    Route::delete(
        '/admin/users/{id}',
        [UserController::class, 'destroy']
    )->name('admin.users.destroy');


    /*
    |--------------------------------------------------------------------------
    | SETTING
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/setting',
        [SettingController::class, 'index']
    )->name('setting');


    Route::put(
        '/setting',
        [SettingController::class, 'update']
    )->name('setting.update');


    /*
    |--------------------------------------------------------------------------
    | PENGELUARAN - ADMIN
    |--------------------------------------------------------------------------
    |
    | Hanya Admin:
    |
    | - Edit
    | - Update
    | - Hapus
    |
    */

    // Form edit
    Route::get(
        '/pengeluaran/{id}/edit',
        [ExpenseController::class, 'edit']
    )->name('pengeluaran.edit');


    // Update
    Route::put(
        '/pengeluaran/{id}',
        [ExpenseController::class, 'update']
    )->name('pengeluaran.update');


    // Hapus
    Route::delete(
        '/pengeluaran/{id}',
        [ExpenseController::class, 'destroy']
    )->name('pengeluaran.destroy');

});


/*
|--------------------------------------------------------------------------
| HALAMAN UTAMA
|--------------------------------------------------------------------------
*/

Route::get('/', function () {


    /*
    |--------------------------------------------------------------------------
    | BELUM LOGIN
    |--------------------------------------------------------------------------
    */

    if (!session('logged_in')) {

        return redirect()
            ->route('dashboard');
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    if (session('user_role') === 'admin') {

        return redirect()
            ->route('dashboard');
    }


    /*
    |--------------------------------------------------------------------------
    | KASIR
    |--------------------------------------------------------------------------
    */

    if (session('user_role') === 'kasir') {

        return redirect()
            ->route('kasir.index');
    }


    /*
    |--------------------------------------------------------------------------
    | ROLE TIDAK VALID
    |--------------------------------------------------------------------------
    */

    return redirect()
        ->route('login');

})->name('home');


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::get('/logout', function (Request $request) {


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
        ->route('dashboard')
        ->with(
            'success',
            'Anda berhasil logout.'
        );

})->name('logout');