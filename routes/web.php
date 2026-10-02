<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use App\Models\Subscription;
use App\Models\Plan;

use App\Http\Controllers\AdminKasirku\DashboardController;
use App\Http\Controllers\AdminKasirku\SettingsController;
use App\Http\Controllers\AdminKasirku\UserController as PlatformUserController;
use App\Http\Controllers\AdminKasirku\StoreController as PlatformStoreController;
use App\Http\Controllers\AdminKasirku\SubscriptionController;
use App\Http\Controllers\AdminKasirku\PlanController as PlatformPlanController;
use App\Http\Controllers\AdminKasirku\SupportController;
use App\Http\Controllers\AdminKasirku\AuditLogController;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReceiptSettingController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\SupportController as OwnerSupportController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\PaymentAccountController;
use App\Http\Controllers\PaymentQrController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FaceRegistrationController;
use App\Http\Controllers\PriceRuleController;



/*
|--------------------------------------------------------------------------
| ABSENSI
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.role', 'active.store'])->group(function () {

    Route::get(
        '/absensi',
        [AttendanceController::class, 'index']
    )->name('attendance.index');

    Route::post(
        '/absensi/check-in',
        [AttendanceController::class, 'checkIn']
    )->name('attendance.check-in');

    Route::post(
        '/absensi/check-out',
        [AttendanceController::class, 'checkOut']
    )->name('attendance.check-out');
    
    Route::get(
    '/absensi/riwayat',
    [AttendanceController::class, 'history']
)->name('attendance.history');

Route::get(
    '/absensi/rekap',
    [AttendanceController::class, 'summary']
)->name('attendance.summary');

});
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
    '/admin-kasirku/pengguna/{user}',
    [PlatformUserController::class, 'show']
)->name('admin-kasirku.users.show');

Route::get(
    '/admin-kasirku/toko',
    [PlatformStoreController::class, 'index']
)->name('admin-kasirku.stores.index');

Route::get('/admin-kasirku/toko/{store}', [PlatformStoreController::class, 'show'])
    ->name('admin-kasirku.stores.show');

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

Route::get(
    '/admin-kasirku/aktivitas',
    [AuditLogController::class, 'index']
)->name('admin-kasirku.audit-log.index');

/*
|--------------------------------------------------------------------------
| PUSAT BANTUAN
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin-kasirku/bantuan',
    [SupportController::class, 'index']
)->name('admin-kasirku.support.index');

Route::get(
    '/admin-kasirku/bantuan/{ticket}',
    [SupportController::class, 'show']
)->name('admin-kasirku.support.show');

Route::put(
    '/admin-kasirku/bantuan/{ticket}/status',
    [SupportController::class, 'updateStatus']
)->name('admin-kasirku.support.status');

Route::put(
    '/admin-kasirku/bantuan/{ticket}/assign',
    [SupportController::class, 'assign']
)->name('admin-kasirku.support.assign');

Route::post(
    '/admin-kasirku/bantuan/{ticket}/reply',
    [SupportController::class, 'reply']
)->name('admin-kasirku.support.reply');

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
        
        Route::post('/pelanggan/ajax', [CustomerController::class, 'ajaxStore'])
    ->middleware('active.store')
    ->name('pelanggan.ajax.store');

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
        
            // =========================
    // TRANSFER ANTAR TOKO
    // =========================

    Route::get('/transfer', [TransferController::class, 'index'])
        ->name('transfer.index');

    Route::get('/transfer/tambah', [TransferController::class, 'create'])
        ->name('transfer.create');

    Route::post('/transfer', [TransferController::class, 'store'])
        ->name('transfer.store');
        
        Route::get('/transfer/{id}', [TransferController::class, 'show'])
    ->name('transfer.show');
    
    Route::post('/transfer/{id}/terima', [TransferController::class, 'receive'])
    ->name('transfer.receive');
    
    Route::get('/absensi/check-face', [AttendanceController::class, 'faceCheck'])
    ->name('attendance.face-check');

Route::post('/absensi/check-face', [AttendanceController::class, 'verifyFace'])
    ->name('attendance.face.verify');

});


/*
|--------------------------------------------------------------------------
| MIDTRANS NOTIFICATION
|--------------------------------------------------------------------------
| Endpoint ini dipanggil langsung oleh Midtrans.
| Tidak membutuhkan login/session owner.
|--------------------------------------------------------------------------
*/

Route::post(
    '/midtrans/notification',
    [PlanController::class, 'midtransNotification']
)->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
->name('midtrans.notification');
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

Route::get(
    '/paket/pembayaran/{payment}/status',
    [PlanController::class, 'paymentStatus']
)->name('paket.payment.status');
    
    Route::post(
    '/paket/pembayaran/{payment}/cek-status',
    [PlanController::class, 'checkStatus']
)->name('paket.payment.check-status');
    
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

/*
|--------------------------------------------------------------------------
| PROSES REGISTRASI
|--------------------------------------------------------------------------
*/

Route::post('/proses-register', function (Request $request) {

    /*
    |--------------------------------------------------------------------------
    | REGISTRASI DENGAN GOOGLE
    |--------------------------------------------------------------------------
    */

    $googleRegister = session('google_register');

    if ($googleRegister) {

        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'nullable|string|max:500',
            'store_phone' => 'nullable|string|max:30',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Pastikan email / Google ID belum digunakan
        |--------------------------------------------------------------------------
        */

        $existingUser = User::where('email', $googleRegister['email'])
            ->orWhere('google_id', $googleRegister['google_id'])
            ->first();

        if ($existingUser) {

            session()->forget('google_register');

            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Akun Google tersebut sudah terdaftar. Silakan masuk.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Buat User + Toko + Subscription
        |--------------------------------------------------------------------------
        */

        $result = DB::transaction(function () use (
            $request,
            $googleRegister
        ) {

            $user = User::create([
                'name' => $googleRegister['name'],
                'email' => $googleRegister['email'],
                'google_id' => $googleRegister['google_id'],

                /*
                | Akun Google tidak membutuhkan password Kasir½M.
                | Tetap diberikan password acak untuk memenuhi
                | struktur database User.
                */
                'password' => Hash::make(
                    \Illuminate\Support\Str::random(64)
                ),

                'role' => 'admin',

                /*
                | Email berasal dari akun Google.
                */
                'email_verified_at' => now(),
            ]);

            $store = Store::create([
                'owner_id' => $user->id,
                'name' => $request->store_name,
                'address' => $request->store_address,
                'phone' => $request->store_phone,
                'is_active' => true,
            ]);

            $user->stores()->attach(
                $store->id,
                [
                    'role' => 'owner',
                ]
            );

            $freePlan = Plan::where(
                'slug',
                'free'
            )->firstOrFail();

            Subscription::create([
                'owner_id' => $user->id,
                'plan_id' => $freePlan->id,
                'starts_at' => now(),
                'ends_at' => null,
                'status' => 'active',
            ]);

            return compact(
                'user',
                'store'
            );
        });

        $user = $result['user'];
        $store = $result['store'];

        /*
        |--------------------------------------------------------------------------
        | Hapus data Google sementara
        |--------------------------------------------------------------------------
        */

        session()->forget('google_register');

        /*
        |--------------------------------------------------------------------------
        | Login otomatis
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        $request->session()->put([
            'logged_in' => true,
            'user_id' => $user->id,
            'username' => $user->name,
            'user_role' => $user->role,
            'active_store_id' => $store->id,
            'is_platform_admin' => false,
        ]);

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Selamat datang, ' .
                $user->name .
                '! Akun Google dan toko Anda berhasil dibuat.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTRASI BIASA
    |--------------------------------------------------------------------------
    */

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

        $user->stores()->attach(
            $store->id,
            [
                'role' => 'owner',
            ]
        );

        $freePlan = Plan::where(
            'slug',
            'free'
        )->firstOrFail();

        Subscription::create([
            'owner_id' => $user->id,
            'plan_id' => $freePlan->id,
            'starts_at' => now(),
            'ends_at' => null,
            'status' => 'active',
        ]);

        return compact(
            'user',
            'store'
        );
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
        'is_platform_admin' => false,
    ]);

    return redirect()
        ->route('dashboard')
        ->with(
            'success',
            'Selamat datang, ' .
            $user->name .
            '! Toko Anda berhasil dibuat.'
        );

})->name('register.process');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');



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
| LUPA PASSWORD
|--------------------------------------------------------------------------
*/

Route::post('/forgot-password', function (Request $request) {

    $request->validate([
        'email' => ['required', 'email'],
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    if ($status === Password::RESET_LINK_SENT) {

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Jika email terdaftar, link reset password telah dikirim. Silakan cek inbox atau folder spam.'
            );
    }

    return back()
        ->withInput($request->only('email'))
        ->with(
            'error',
            'Link reset password gagal dikirim. Silakan coba lagi.'
        );

})->name('password.email');

/*
|--------------------------------------------------------------------------
| RESET PASSWORD
|--------------------------------------------------------------------------
*/

Route::get('/reset-password/{token}', function (
    Request $request,
    string $token
) {
    return view('login', [
        'resetToken' => $token,
        'resetEmail' => $request->query('email'),
    ]);
})->name('password.reset');


Route::post('/reset-password', function (Request $request) {

    $request->validate([
        'token' => ['required'],
        'email' => ['required', 'email'],
        'password' => ['required', 'min:8', 'confirmed'],
    ]);

    $status = Password::reset(
        $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        ),
        function (User $user, string $password) {

            $user->forceFill([
                'password' => $password,
                'remember_token' => \Illuminate\Support\Str::random(60),
            ])->save();
        }
    );

    if ($status === Password::PASSWORD_RESET) {

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Password berhasil diubah. Silakan masuk menggunakan password baru.'
            );
    }

    return back()
        ->withInput($request->only('email'))
        ->with(
            'error',
            'Link reset password tidak valid atau sudah kedaluwarsa.'
        );

})->name('password.update');


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
    
    Route::get(
    '/profil/wajah',
    [FaceRegistrationController::class, 'create']
)->name('profil.face');

Route::post(
    '/profil/wajah',
    [FaceRegistrationController::class, 'store']
)->name('profil.face.store');

Route::delete(
    '/profil/wajah',
    [FaceRegistrationController::class, 'destroy']
)->name('profil.face.destroy');
    
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
| PUSAT BANTUAN - OWNER
|--------------------------------------------------------------------------
*/

Route::middleware('auth.role')->group(function () {

    Route::get(
        '/bantuan',
        [OwnerSupportController::class, 'index']
    )->name('support.index');

    Route::get(
        '/bantuan/buat',
        [OwnerSupportController::class, 'create']
    )->name('support.create');

    Route::post(
        '/bantuan',
        [OwnerSupportController::class, 'store']
    )->name('support.store');

    Route::get(
        '/bantuan/{ticket}',
        [OwnerSupportController::class, 'show']
    )->name('support.show');

    Route::post(
        '/bantuan/{ticket}/pesan',
        [OwnerSupportController::class, 'message']
    )->name('support.message');
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


Route::get('/backup', [BackupController::class, 'index'])
        ->name('backup.index');

    Route::get('/backup/download', [BackupController::class, 'download'])
        ->name('backup.download');
        
        Route::get('/backup/restore', [BackupController::class, 'restoreIndex'])
    ->name('backup.restore');

Route::post('/backup/restore/preview', [BackupController::class, 'restorePreview'])
    ->name('backup.restore.preview');
    
    Route::post('/backup/restore', [BackupController::class, 'restore'])
    ->name('backup.restore.execute');
    
    Route::get(
    '/pengaturan-pembayaran',
    [PaymentAccountController::class, 'index']
)->name('payment-settings.index');

Route::post(
    '/pengaturan-pembayaran/rekening',
    [PaymentAccountController::class, 'store']
)->name('payment-accounts.store');

Route::put(
    '/pengaturan-pembayaran/rekening/{paymentAccount}',
    [PaymentAccountController::class, 'update']
)->name('payment-accounts.update');

Route::delete(
    '/pengaturan-pembayaran/rekening/{paymentAccount}',
    [PaymentAccountController::class, 'destroy']
)->name('payment-accounts.destroy');

Route::patch(
    '/pengaturan-pembayaran/rekening/{paymentAccount}/toggle',
    [PaymentAccountController::class, 'toggle']
)->name('payment-accounts.toggle');


Route::post(
    '/pengaturan-pembayaran/qr',
    [PaymentQrController::class, 'store']
)->name('payment-qrs.store');

Route::delete(
    '/pengaturan-pembayaran/qr/{paymentQr}',
    [PaymentQrController::class, 'destroy']
)->name('payment-qrs.destroy');

Route::patch(
    '/pengaturan-pembayaran/qr/{paymentQr}/toggle',
    [PaymentQrController::class, 'toggle']
)->name('payment-qrs.toggle');
        
        
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
    
    Route::get(
    '/admin/users/{id}/activity',
    [UserController::class, 'activity']
)->name('admin.users.activity');
    


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
    
    
    // =========================
// HARGA KHUSUS & PROMOSI
// =========================

Route::get(
    '/harga-khusus',
    [PriceRuleController::class, 'index']
)->name('price-rules.index');

Route::post(
    '/harga-khusus',
    [PriceRuleController::class, 'store']
)->name('price-rules.store');

Route::patch(
    '/harga-khusus/{priceRule}/toggle',
    [PriceRuleController::class, 'toggle']
)->name('price-rules.toggle');

Route::delete(
    '/harga-khusus/{priceRule}',
    [PriceRuleController::class, 'destroy']
)->name('price-rules.destroy');
    /*
|--------------------------------------------------------------------------
| PENGATURAN STRUK
|--------------------------------------------------------------------------
*/

Route::get(
    '/pengaturan-struk',
    [ReceiptSettingController::class, 'index']
)->name('receipt-settings.index');

Route::put(
    '/pengaturan-struk',
    [ReceiptSettingController::class, 'update']
)->name('receipt-settings.update');

Route::delete(
    '/pengaturan-struk/logo',
    [ReceiptSettingController::class, 'deleteLogo']
)->name('receipt-settings.logo.delete');


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
    | PLATFORM ADMIN
    |--------------------------------------------------------------------------
    */

    if (session('is_platform_admin')) {
        return redirect()
            ->route('admin-kasirku.dashboard');
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
    ->route('login')
    ->with(
        'success',
        'Anda berhasil logout.'
    );

})->name('logout');