<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use App\Models\Plan;

use App\Http\Controllers\AdminKasirku\DashboardController;
use App\Http\Controllers\AdminKasirku\SettingsController;
use App\Http\Controllers\AdminKasirku\UserController as PlatformUserController;
use App\Http\Controllers\AdminKasirku\StoreController as PlatformStoreController;
use App\Http\Controllers\AdminKasirku\SubscriptionController;
use App\Http\Controllers\AdminKasirku\PlanController as PlatformPlanController;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;



/*
|--------------------------------------------------------------------------
| ADMIN KASIRKU
| PLATFORM ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware('platform.admin')->group(function () {

    Route::get(
        '/admin-kasirku',
        [DashboardController::class, 'index']
    )->name('admin-kasirku.dashboard');

    Route::get(
        '/admin-kasirku/pengaturan',
        [SettingsController::class, 'index']
    )->name('admin-kasirku.settings');

    Route::put(
        '/admin-kasirku/pengaturan/profil',
        [SettingsController::class, 'updateProfile']
    )->name('admin-kasirku.settings.profile');

    Route::put(
        '/admin-kasirku/pengaturan/password',
        [SettingsController::class, 'updatePassword']
    )->name('admin-kasirku.settings.password');

    Route::get(
    '/admin-kasirku/pengguna',
    [PlatformUserController::class, 'index']
)->name('admin-kasirku.users.index');

Route::get(
    '/admin-kasirku/toko',
    [PlatformStoreController::class, 'index']
)->name('admin-kasirku.stores.index');

Route::get(
    '/admin-kasirku/langganan',
    [SubscriptionController::class, 'index']
)->name('admin-kasirku.subscriptions.index');

Route::get(
    '/admin-kasirku/langganan/{subscription}',
    [SubscriptionController::class, 'show']
)->name('admin-kasirku.subscriptions.show');

Route::get(
    '/admin-kasirku/langganan/{subscription}/edit',
    [SubscriptionController::class, 'edit']
)->name('admin-kasirku.subscriptions.edit');

Route::put(
    '/admin-kasirku/langganan/{subscription}',
    [SubscriptionController::class, 'update']
)->name('admin-kasirku.subscriptions.update');

Route::get(
    '/admin-kasirku/paket',
    [PlatformPlanController::class, 'index']
)->name('admin-kasirku.plans.index');

Route::get(
    '/admin-kasirku/paket/{plan}/edit',
    [PlatformPlanController::class, 'edit']
)->name('admin-kasirku.plans.edit');

Route::put(
    '/admin-kasirku/paket/{plan}',
    [PlatformPlanController::class, 'update']
)->name('admin-kasirku.plans.update');

});
/*
|--------------------------------------------------------------------------
| CUSTOMER / SUPPLIER / PEMBELIAN
| WAJIB LOGIN + TOKO AKTIF
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.role', 'active.store'])->group(function () {

    // =========================
    // PELANGGAN
    // =========================

    Route::get('/pelanggan', [CustomerController::class, 'index'])
        ->name('pelanggan.index');

    Route::get('/pelanggan/tambah', [CustomerController::class, 'create'])
        ->name('pelanggan.create');

    Route::post('/pelanggan', [CustomerController::class, 'store'])
        ->name('pelanggan.store');

    Route::get('/pelanggan/{id}', [CustomerController::class, 'show'])
        ->name('pelanggan.show');

    // =========================
    // PIUTANG / UTANG PELANGGAN
    // =========================

    Route::get('/pelanggan/{id}/utang/tambah', [CustomerController::class, 'createDebt'])
        ->name('pelanggan.debt.create');

    Route::post('/pelanggan/{id}/utang', [CustomerController::class, 'storeDebt'])
        ->name('pelanggan.debt.store');

    Route::get('/pelanggan/utang/{id}/bayar', [CustomerController::class, 'createDebtPayment'])
        ->name('pelanggan.debt.payment.create');

    Route::post('/pelanggan/utang/{id}/bayar', [CustomerController::class, 'storeDebtPayment'])
        ->name('pelanggan.debt.payment.store');


    // =========================
    // SUPPLIER
    // =========================

    Route::get('/supplier', [SupplierController::class, 'index'])
        ->name('supplier.index');

    Route::get('/supplier/tambah', [SupplierController::class, 'create'])
        ->name('supplier.create');

    Route::post('/supplier', [SupplierController::class, 'store'])
        ->name('supplier.store');

    Route::get('/supplier/{id}', [SupplierController::class, 'show'])
        ->name('supplier.show');

    Route::get('/supplier/{id}/edit', [SupplierController::class, 'edit'])
        ->name('supplier.edit');

    Route::put('/supplier/{id}', [SupplierController::class, 'update'])
        ->name('supplier.update');


    // =========================
    // PEMBELIAN
    // =========================

    Route::get('/pembelian', [PurchaseController::class, 'index'])
        ->name('purchase.index');

    Route::get('/pembelian/tambah', [PurchaseController::class, 'create'])
        ->name('purchase.create');

    Route::post('/pembelian', [PurchaseController::class, 'store'])
        ->name('purchase.store');

    Route::get('/pembelian/{id}', [PurchaseController::class, 'show'])
        ->name('purchase.show');

});


/*
|--------------------------------------------------------------------------
| PAKET / LANGGANAN
| PUBLIK
|--------------------------------------------------------------------------
*/

Route::get('/paket', [PlanController::class, 'index'])
    ->name('paket');

    Route::get('/paket/riwayat-pembayaran', [PlanController::class, 'paymentHistory'])
    ->name('paket.payment.history');

    Route::get('/paket/riwayat-pembayaran/{payment}', [PlanController::class, 'paymentDetail'])
    ->name('paket.payment.detail');

Route::post('/paket/select/{plan}', [PlanController::class, 'select'])
    ->name('paket.select');

Route::get('/paket/pembayaran/{plan}', [PlanController::class, 'payment'])
    ->name('paket.payment');

Route::post('/paket/pembayaran/{plan}/buat', [PlanController::class, 'createPayment'])
    ->name('paket.payment.create');

Route::get('/paket/pembayaran/menunggu/{payment}', [PlanController::class, 'paymentPending'])
    ->name('paket.payment.pending');

Route::post('/paket/pembayaran/{payment}/success', [PlanController::class, 'paymentSuccess'])
    ->name('paket.payment.success');

    Route::get('/paket/referral/validate', [PlanController::class, 'validateReferral'])
    ->name('paket.referral.validate');

Route::get('/produk-image/{path}', function ($path) {

    $path = urldecode($path);

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return response()->file(
        Storage::disk('public')->path($path)
    );

})->where('path', '.*')->name('produk.image');

// Halaman Registrasi
Route::get('/register', function () {

    if (session('logged_in')) {
        return redirect()->route('dashboard');
    }

    return view('register');

})->name('register');

// PROSES REGISTRASI
Route::post('/proses-register', function (Request $request) {

    $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|max:255|unique:users,email',
    'password' => 'required|string|min:8|confirmed',

    'store_name' => 'required|string|max:255',
    'store_address' => 'nullable|string|max:500',
    'store_phone' => 'nullable|string|max:30',
]);

$result = DB::transaction(function () use ($request) {

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'admin',
    ]);

    $store = Store::create([
        'owner_id' => $user->id,
        'name' => $request->store_name,
        'address' => $request->store_address,
        'phone' => $request->store_phone,
        'is_active' => true,
    ]);

    $user->stores()->attach($store->id, [
        'role' => 'owner',
    ]);

    $freePlan = Plan::where('slug', 'free')->firstOrFail();

Subscription::create([
    'owner_id' => $user->id,
    'plan_id' => $freePlan->id,
    'starts_at' => now(),
    'ends_at' => null,
    'status' => 'active',
]);

    return compact('user', 'store');
});

$user = $result['user'];
$store = $result['store'];

$request->session()->regenerate();

$request->session()->put([
    'logged_in' => true,
    'user_id' => $user->id,
    'username' => $user->name,
    'user_role' => $user->role,
    'active_store_id' => $store->id,
]);

return redirect()
    ->route('dashboard')
    ->with(
        'success',
        'Selamat datang, ' . $user->name . '! Toko Anda berhasil dibuat.'
    );

})->name('register.process');



// Halaman Login
Route::get('/login', function () {

    if (session('logged_in')) {

    if (session('is_platform_admin')) {
        return redirect()->route('admin-kasirku.dashboard');
    }

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
| ADMIN KASIRKU
|--------------------------------------------------------------------------
*/

if ($user->is_platform_admin) {

    $request->session()->put([
        'logged_in' => true,
        'user_id' => $user->id,
        'username' => $user->name,
        'user_role' => $user->role,
        'is_platform_admin' => true,
    ]);

    return redirect()
        ->route('admin-kasirku.dashboard')
        ->with(
            'success',
            'Selamat datang di Dashboard Admin KasirKU!'
        );
}


/*
|--------------------------------------------------------------------------
| USER TOKO
|--------------------------------------------------------------------------
*/

$activeStore = $user->stores()
    ->orderBy('stores.id')
    ->first();

$request->session()->put([
    'logged_in' => true,
    'user_id' => $user->id,
    'username' => $user->name,
    'user_role' => $user->role,
    'active_store_id' => $activeStore?->id,
    'is_platform_admin' => false,
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
| SWITCH TOKO
|--------------------------------------------------------------------------
*/

Route::post(
    '/switch-store/{id}',
    [StoreController::class, 'switch']
)->middleware('auth.role')
->name('store.switch');

Route::get(
    '/tambah-toko',
    [StoreController::class, 'create']
)->middleware('auth.role:admin')
->name('store.create');

Route::post(
    '/tambah-toko',
    [StoreController::class, 'store']
)->middleware('auth.role:admin')
->name('store.store');


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
      )->middleware('active.store')
      ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | PRODUK
    |--------------------------------------------------------------------------
    */

    Route::get(
    '/produk',
    [ProductController::class, 'index']
)->middleware('active.store')
->name('produk.index');


    // Tambah Produk / Restock
    Route::post(
        '/produk',
        [ProductController::class, 'store']
    )->middleware('auth.role')
    ->name('produk.store');

    Route::get(
    '/produk/scan/{sku}',
    [ProductController::class, 'scanBySku']
)->middleware('active.store')
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
)->middleware('active.store')
->name('kasir.index');


    // Simpan transaksi
Route::post(
    '/transaksi',
    [TransactionController::class, 'store']
)->middleware('auth.role')
->name('transaksi.store');

// Cetak / tampilkan struk PDF
Route::get(
    '/transaksi/{id}/struk-pdf',
    [TransactionController::class, 'receiptPdf']
)->middleware('auth.role')
->name('transaksi.struk.pdf');


    /*
    |--------------------------------------------------------------------------
    | RIWAYAT TRANSAKSI
    |--------------------------------------------------------------------------
    */

    Route::get('/riwayat', [TransactionController::class, 'index'])
    ->middleware('active.store')
    ->name('riwayat');


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

    Route::get('/pengeluaran', [ExpenseController::class, 'index'])
    ->middleware('active.store')
    ->name('pengeluaran');


    Route::post(
    '/pengeluaran',
    [ExpenseController::class, 'store']
)->middleware('auth.role')
->name('pengeluaran.store');


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

    Route::get('/laporan', [LaporanController::class, 'index'])
    ->middleware('active.store')
    ->name('laporan');

    Route::get('/laporan/piutang', [LaporanController::class, 'piutang'])
    ->middleware('active.store')
    ->name('laporan.piutang');


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

        return view('welcome.welcome');
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