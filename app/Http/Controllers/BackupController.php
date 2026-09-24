<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupController extends Controller
{
  
  /**
 * Halaman backup data.
 */
public function index()
{
    if (!session('logged_in')) {
        return redirect()
            ->route('login')
            ->with('error', 'Silakan login terlebih dahulu.');
    }

    $userId = (int) session('user_id');

    $stores = Store::where('owner_id', $userId)
        ->orderBy('id')
        ->get();

    return view('backup.index', compact('stores'));
}

/**
 * Halaman restore data.
 */
public function restoreIndex()
{
    if (!session('logged_in')) {
        return redirect()
            ->route('login')
            ->with('error', 'Silakan login terlebih dahulu.');
    }

    return view('backup.restore');
}


/**
 * Upload dan validasi file backup.
 */
public function restorePreview(Request $request)
{
    if (!session('logged_in')) {
        return redirect()
            ->route('login')
            ->with('error', 'Silakan login terlebih dahulu.');
    }

    $request->validate([
        'backup_file' => [
            'required',
            'file',
            'mimes:zip',
            'max:102400',
        ],
    ]);

    $file = $request->file('backup_file');

    $backupId = 'restore-' . now()->format('Ymd-His') . '-' . uniqid();

    $directory = storage_path('app/restore-temp');

    if (!File::exists($directory)) {
        File::makeDirectory($directory, 0755, true);
    }

    $zipPath = $directory . '/' . $backupId . '.zip';

    $file->move($directory, $backupId . '.zip');

    $zip = new ZipArchive();

    if ($zip->open($zipPath) !== true) {
        File::delete($zipPath);

        return back()->with(
            'error',
            'File backup tidak dapat dibuka.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ambil metadata backup
    |--------------------------------------------------------------------------
    */

    $backupJson = $zip->getFromName(
        'kasirku-backup/backup.json'
    );

    if ($backupJson === false) {
        $zip->close();
        File::delete($zipPath);

        return back()->with(
            'error',
            'File backup tidak valid. backup.json tidak ditemukan.'
        );
    }

    $backup = json_decode($backupJson, true);

    if (!is_array($backup)) {
        $zip->close();
        File::delete($zipPath);

        return back()->with(
            'error',
            'Metadata backup tidak valid.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validasi aplikasi
    |--------------------------------------------------------------------------
    */

    if (($backup['application'] ?? null) !== 'KasirKU') {
        $zip->close();
        File::delete($zipPath);

        return back()->with(
            'error',
            'File ini bukan backup KasirKU.'
        );
    }

    if (($backup['format_version'] ?? null) !== '1.0') {
        $zip->close();
        File::delete($zipPath);

        return back()->with(
            'error',
            'Versi backup tidak didukung.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validasi owner
    |--------------------------------------------------------------------------
    */

    $userId = (int) session('user_id');

    if ((int) ($backup['owner_id'] ?? 0) !== $userId) {
        $zip->close();
        File::delete($zipPath);

        return back()->with(
            'error',
            'Backup ini bukan milik akun Anda.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Daftar tabel yang wajib ada
    |--------------------------------------------------------------------------
    */

    $tables = [
        'users',
        'stores',
        'store_users',
        'products',
        'product_histories',
        'customers',
        'debts',
        'debt_payments',
        'suppliers',
        'purchases',
        'purchase_items',
        'transactions',
        'transaction_items',
        'expenses',
        'settings',
        'transfers',
        'transfer_items',
    ];

    $counts = [];

    foreach ($tables as $table) {
        $json = $zip->getFromName(
            'kasirku-backup/' . $table . '.json'
        );

        if ($json === false) {
            $zip->close();
            File::delete($zipPath);

            return back()->with(
                'error',
                "File backup tidak lengkap. {$table}.json tidak ditemukan."
            );
        }

        $rows = json_decode($json, true);

        if (!is_array($rows)) {
            $zip->close();
            File::delete($zipPath);

            return back()->with(
                'error',
                "Data {$table}.json tidak valid."
            );
        }

        $counts[$table] = count($rows);
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi toko
    |--------------------------------------------------------------------------
    */

    $storesJson = $zip->getFromName(
        'kasirku-backup/stores.json'
    );

    $backupStores = json_decode($storesJson, true);

    $zip->close();

    /*
    |--------------------------------------------------------------------------
    | Simpan path sementara untuk tahap restore berikutnya
    |--------------------------------------------------------------------------
    */

    session([
        'restore_backup_path' => $zipPath,
        'restore_backup_metadata' => $backup,
    ]);

    return view('backup.restore-preview', [
        'backup' => $backup,
        'backupStores' => is_array($backupStores)
            ? $backupStores
            : [],
        'counts' => $counts,
    ]);
}

/**
 * Restore data dari backup yang sudah divalidasi.
 */
public function restore()
{
    if (!session('logged_in')) {
        return redirect()
            ->route('login')
            ->with('error', 'Silakan login terlebih dahulu.');
    }

    $userId = (int) session('user_id');

    $zipPath = session('restore_backup_path');

    if (!$zipPath || !File::exists($zipPath)) {
        return redirect()
            ->route('backup.restore')
            ->with('error', 'File backup sudah tidak tersedia. Silakan upload ulang.');
    }

    $zip = new ZipArchive();

    if ($zip->open($zipPath) !== true) {
        File::delete($zipPath);

        session()->forget([
            'restore_backup_path',
            'restore_backup_metadata',
        ]);

        return redirect()
            ->route('backup.restore')
            ->with('error', 'File backup tidak dapat dibuka.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper membaca JSON dari ZIP
    |--------------------------------------------------------------------------
    */

    $readTable = function (string $table) use ($zip): array {
        $content = $zip->getFromName(
            'kasirku-backup/' . $table . '.json'
        );

        if ($content === false) {
            throw new \RuntimeException(
                "File {$table}.json tidak ditemukan."
            );
        }

        $rows = json_decode($content, true);

        if (!is_array($rows)) {
            throw new \RuntimeException(
                "Data {$table}.json tidak valid."
            );
        }

        return $rows;
    };

    try {

        /*
        |--------------------------------------------------------------------------
        | Validasi metadata lagi
        |--------------------------------------------------------------------------
        */

        $metadataJson = $zip->getFromName(
            'kasirku-backup/backup.json'
        );

        if ($metadataJson === false) {
            throw new \RuntimeException(
                'backup.json tidak ditemukan.'
            );
        }

        $backup = json_decode($metadataJson, true);

        if (!is_array($backup)) {
            throw new \RuntimeException(
                'Metadata backup tidak valid.'
            );
        }

        if (($backup['application'] ?? null) !== 'KasirKU') {
            throw new \RuntimeException(
                'Backup bukan berasal dari KasirKU.'
            );
        }

        if (($backup['format_version'] ?? null) !== '1.0') {
            throw new \RuntimeException(
                'Versi backup tidak didukung.'
            );
        }

        if ((int) ($backup['owner_id'] ?? 0) !== $userId) {
            throw new \RuntimeException(
                'Backup bukan milik akun Anda.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Baca seluruh tabel
        |--------------------------------------------------------------------------
        */
        $users = $readTable('users');
        $stores = $readTable('stores');
        $storeUsers = $readTable('store_users');

        $products = $readTable('products');
        $productHistories = $readTable('product_histories');

        $customers = $readTable('customers');

        $debts = $readTable('debts');
        $debtPayments = $readTable('debt_payments');

        $suppliers = $readTable('suppliers');

        $purchases = $readTable('purchases');
        $purchaseItems = $readTable('purchase_items');

        $transactions = $readTable('transactions');
        $transactionItems = $readTable('transaction_items');

        $expenses = $readTable('expenses');
        $settings = $readTable('settings');

        $transfers = $readTable('transfers');
        $transferItems = $readTable('transfer_items');


        /*
        |--------------------------------------------------------------------------
        | Tutup ZIP sebelum transaksi database
        |--------------------------------------------------------------------------
        */

        $zip->close();


        /*
        |--------------------------------------------------------------------------
        | MAPPING ID
        |--------------------------------------------------------------------------
        */
        
        $userMap = [];
        $storeMap = [];
        $productMap = [];
        $customerMap = [];
        $debtMap = [];
        $supplierMap = [];
        $purchaseMap = [];
        $transactionMap = [];
        $transferMap = [];



      /*
|--------------------------------------------------------------------------
| MAPPING USERS
|--------------------------------------------------------------------------
*/

foreach ($users as $row) {

    $oldUserId = (int) ($row['id'] ?? 0);

    if (!$oldUserId) {
        continue;
    }

    // Owner backup diarahkan ke owner yang sedang login.
    if ($oldUserId === (int) ($backup['owner_id'] ?? 0)) {
        $userMap[$oldUserId] = $userId;
        continue;
    }

    $existingUser = null;

    // Cari berdasarkan email.
    if (!empty($row['email'])) {
        $existingUser = \App\Models\User::where(
            'email',
            $row['email']
        )->first();
    }

    // Jika email tidak ditemukan, cari berdasarkan username.
    if (!$existingUser && !empty($row['username'])) {
        $existingUser = \App\Models\User::where(
            'username',
            $row['username']
        )->first();
    }

    if ($existingUser) {
        $userMap[$oldUserId] = (int) $existingUser->id;
    }
}
        /*
        |--------------------------------------------------------------------------
        | TRANSACTION DATABASE
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $userId,
           $users,
           $backup,
            $stores,
            $storeUsers,

            $products,
            $productHistories,

            $customers,

            $debts,
            $debtPayments,

            $suppliers,

            $purchases,
            $purchaseItems,

            $transactions,
            $transactionItems,

            $expenses,
            $settings,

            $transfers,
            $transferItems,

            &$userMap,
            &$storeMap,
            &$productMap,
            &$customerMap,
            &$debtMap,
            &$supplierMap,
            &$purchaseMap,
            &$transactionMap,
            &$transferMap
        ) {

            /*
            |--------------------------------------------------------------------------
            | 1. STORES
            |--------------------------------------------------------------------------
            */

            foreach ($stores as $row) {

                $oldId = (int) $row['id'];

                $existingStore = Store::where('owner_id', $userId)
                    ->where('name', $row['name'] ?? '')
                    ->first();

                if ($existingStore) {

                    $storeMap[$oldId] = $existingStore->id;

                } else {

                    $newStore = Store::create([
                        'owner_id' => $userId,
                        'name' => $row['name'] ?? 'Toko',
                        'address' => $row['address'] ?? null,
                        'phone' => $row['phone'] ?? null,
                        'is_active' => $row['is_active'] ?? 1,
                    ]);

                    $storeMap[$oldId] = $newStore->id;
                }
            }


/*
|--------------------------------------------------------------------------
| 2. STORE USERS
|--------------------------------------------------------------------------
*/

foreach ($storeUsers as $row) {

    $oldStoreId = (int) $row['store_id'];
    $oldUserId = (int) $row['user_id'];

    if (!isset($storeMap[$oldStoreId])) {
        continue;
    }

    // User dari backup harus sudah berhasil dipetakan
    // ke user yang ada di database sekarang.
    if (!isset($userMap[$oldUserId])) {
        continue;
    }

    $newStoreId = $storeMap[$oldStoreId];
    $newUserId = $userMap[$oldUserId];

    $exists = DB::table('store_user')
        ->where('store_id', $newStoreId)
        ->where('user_id', $newUserId)
        ->exists();

    if (!$exists) {

        DB::table('store_user')->insert([
            'store_id' => $newStoreId,
            'user_id' => $newUserId,
            'role' => $row['role'] ?? 'kasir',
            'created_at' => $row['created_at'] ?? now(),
            'updated_at' => $row['updated_at'] ?? now(),
        ]);
    }
}


            /*
            |--------------------------------------------------------------------------
            | 3. PRODUCTS
            |--------------------------------------------------------------------------
            */

            foreach ($products as $row) {

                $oldId = (int) $row['id'];

                $oldStoreId = $row['store_id'] !== null
                    ? (int) $row['store_id']
                    : null;

                if (!$oldStoreId || !isset($storeMap[$oldStoreId])) {
                    continue;
                }

                $newStoreId = $storeMap[$oldStoreId];

                $existingProduct = DB::table('products')
                    ->where('store_id', $newStoreId)
                    ->where('sku', $row['sku'])
                    ->first();

                if ($existingProduct) {

                    $productMap[$oldId] = $existingProduct->id;

                } else {

                    $newId = DB::table('products')->insertGetId([
                        'sku' => $row['sku'],
                        'image' => $row['image'] ?? null,
                        'name' => $row['name'],
                        'category' => $row['category'],
                        'brand' => $row['brand'] ?? null,
                        'capital_price' => $row['capital_price'] ?? 0,
                        'price' => $row['price'] ?? 0,
                        'stock' => $row['stock'] ?? 0,
                        'store_id' => $newStoreId,
                        'created_at' => $row['created_at'] ?? now(),
                        'updated_at' => now(),
                    ]);

                    $productMap[$oldId] = $newId;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | 4. PRODUCT HISTORIES
            |--------------------------------------------------------------------------
            */

foreach ($productHistories as $row) {

    $oldProductId = (int) ($row['product_id'] ?? 0);

    $oldStoreId = ($row['store_id'] ?? null) !== null
        ? (int) $row['store_id']
        : null;

    if (
        !$oldProductId ||
        !isset($productMap[$oldProductId]) ||
        !$oldStoreId ||
        !isset($storeMap[$oldStoreId])
    ) {
        continue;
    }

    $newProductId = $productMap[$oldProductId];
    $newStoreId = $storeMap[$oldStoreId];

    $existingQuery = DB::table('product_histories')
        ->where('product_id', $newProductId)
        ->where('store_id', $newStoreId)
        ->where('sku', $row['sku'])
        ->where('name', $row['name'])
        ->where('added_stock', $row['added_stock'])
        ->where('status_type', $row['status_type']);

    // created_at sebagai identitas tambahan
    if (($row['created_at'] ?? null) === null) {
        $existingQuery->whereNull('created_at');
    } else {
        $existingQuery->where('created_at', $row['created_at']);
    }

    $existing = $existingQuery->first();

    if ($existing) {
        continue;
    }

    DB::table('product_histories')->insert([
        'product_id' => $newProductId,
        'sku' => $row['sku'],
        'name' => $row['name'],
        'added_stock' => $row['added_stock'],
        'status_type' => $row['status_type'],
        'store_id' => $newStoreId,
        'created_at' => $row['created_at'] ?? now(),
        'updated_at' => now(),
    ]);
}


            /*
            |--------------------------------------------------------------------------
            | 5. CUSTOMERS
            |--------------------------------------------------------------------------
            */

            foreach ($customers as $row) {

                $oldId = (int) $row['id'];
                $oldStoreId = (int) $row['store_id'];

                if (!isset($storeMap[$oldStoreId])) {
                    continue;
                }

                $newStoreId = $storeMap[$oldStoreId];

                $existing = DB::table('customers')
                    ->where('store_id', $newStoreId)
                    ->where('name', $row['name'])
                    ->where('phone', $row['phone'] ?? null)
                    ->first();

                if ($existing) {

                    $customerMap[$oldId] = $existing->id;

                } else {

                    $newId = DB::table('customers')->insertGetId([
                        'store_id' => $newStoreId,
                        'name' => $row['name'],
                        'phone' => $row['phone'] ?? null,
                        'address' => $row['address'] ?? null,
                        'created_at' => $row['created_at'] ?? now(),
                        'updated_at' => now(),
                    ]);

                    $customerMap[$oldId] = $newId;
                }
            }


/*
|--------------------------------------------------------------------------
| 6. DEBTS
|--------------------------------------------------------------------------
*/

foreach ($debts as $row) {

    $oldId = (int) ($row['id'] ?? 0);
    $oldStoreId = (int) ($row['store_id'] ?? 0);
    $oldCustomerId = (int) ($row['customer_id'] ?? 0);

    if (
        !$oldId ||
        !isset($storeMap[$oldStoreId]) ||
        !isset($customerMap[$oldCustomerId])
    ) {
        continue;
    }

    $newStoreId = $storeMap[$oldStoreId];
    $newCustomerId = $customerMap[$oldCustomerId];

    /*
    |--------------------------------------------------------------------------
    | Cek apakah hutang yang sama sudah ada
    |--------------------------------------------------------------------------
    */

    $existingDebtQuery = DB::table('debts')
        ->where('store_id', $newStoreId)
        ->where('customer_id', $newCustomerId)
        ->where('amount', $row['amount']);

    if (($row['description'] ?? null) === null) {
        $existingDebtQuery->whereNull('description');
    } else {
        $existingDebtQuery->where(
            'description',
            $row['description']
        );
    }

    if (($row['debt_date'] ?? null) === null) {
        $existingDebtQuery->whereNull('debt_date');
    } else {
        $existingDebtQuery->where(
            'debt_date',
            $row['debt_date']
        );
    }

    if (($row['due_date'] ?? null) === null) {
        $existingDebtQuery->whereNull('due_date');
    } else {
        $existingDebtQuery->where(
            'due_date',
            $row['due_date']
        );
    }

    $existingDebt = $existingDebtQuery->first();

    /*
    |--------------------------------------------------------------------------
    | Jika sudah ada, gunakan ID yang sudah ada
    |--------------------------------------------------------------------------
    */

    if ($existingDebt) {

        $debtMap[$oldId] = $existingDebt->id;

    } else {

        $newId = DB::table('debts')->insertGetId([
            'store_id' => $newStoreId,
            'customer_id' => $newCustomerId,
            'user_id' => $userMap[(int) ($row['user_id'] ?? 0)] ?? $userId,
            'description' => $row['description'] ?? null,
            'amount' => $row['amount'],
            'paid_amount' => $row['paid_amount'] ?? 0,
            'debt_date' => $row['debt_date'] ?? null,
            'due_date' => $row['due_date'] ?? null,
            'status' => $row['status'] ?? 'unpaid',
            'created_at' => $row['created_at'] ?? now(),
            'updated_at' => now(),
        ]);

        $debtMap[$oldId] = $newId;
    }
}


            /*
            |--------------------------------------------------------------------------
            | 7. DEBT PAYMENTS
            |--------------------------------------------------------------------------
            */

            foreach ($debtPayments as $row) {
    $oldDebtId = (int) ($row['debt_id'] ?? 0);

    if (!isset($debtMap[$oldDebtId])) {
        continue;
    }

    $newDebtId = $debtMap[$oldDebtId];

    $existingQuery = DB::table('debt_payments')
        ->where('debt_id', $newDebtId)
        ->where('amount', $row['amount'])
        ->where('payment_date', $row['payment_date']);

    if (($row['note'] ?? null) === null) {
        $existingQuery->whereNull('note');
    } else {
        $existingQuery->where('note', $row['note']);
    }

    if (($row['created_at'] ?? null) === null) {
        $existingQuery->whereNull('created_at');
    } else {
        $existingQuery->where('created_at', $row['created_at']);
    }

    $existing = $existingQuery->first();

    if ($existing) {
        continue;
    }

    DB::table('debt_payments')->insert([
        'debt_id' => $newDebtId,
        'user_id' => $userMap[(int) ($row['user_id'] ?? 0)] ?? $userId,
        'amount' => $row['amount'],
        'payment_date' => $row['payment_date'],
        'note' => $row['note'] ?? null,
        'created_at' => $row['created_at'] ?? now(),
        'updated_at' => now(),
    ]);
}


            /*
            |--------------------------------------------------------------------------
            | 8. SUPPLIERS
            |--------------------------------------------------------------------------
            */

            foreach ($suppliers as $row) {

                $oldId = (int) $row['id'];
                $oldStoreId = (int) $row['store_id'];

                if (!isset($storeMap[$oldStoreId])) {
                    continue;
                }

                $existing = DB::table('suppliers')
                    ->where('store_id', $storeMap[$oldStoreId])
                    ->where('name', $row['name'])
                    ->where('phone', $row['phone'] ?? null)
                    ->first();

                if ($existing) {

                    $supplierMap[$oldId] = $existing->id;

                } else {

                    $newId = DB::table('suppliers')->insertGetId([
                        'store_id' => $storeMap[$oldStoreId],
                        'name' => $row['name'],
                        'phone' => $row['phone'] ?? null,
                        'address' => $row['address'] ?? null,
                        'notes' => $row['notes'] ?? null,
                        'created_at' => $row['created_at'] ?? now(),
                        'updated_at' => now(),
                    ]);

                    $supplierMap[$oldId] = $newId;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | 9. PURCHASES
            |--------------------------------------------------------------------------
            */

            foreach ($purchases as $row) {
    $oldId = (int) ($row['id'] ?? 0);
    $oldStoreId = (int) ($row['store_id'] ?? 0);
    $oldSupplierId = (int) ($row['supplier_id'] ?? 0);

    if (
        !$oldId ||
        !isset($storeMap[$oldStoreId]) ||
        !isset($supplierMap[$oldSupplierId])
    ) {
        continue;
    }

    $newStoreId = $storeMap[$oldStoreId];
    $newSupplierId = $supplierMap[$oldSupplierId];

    $existingQuery = DB::table('purchases')
        ->where('store_id', $newStoreId)
        ->where('supplier_id', $newSupplierId)
        ->where('purchase_date', $row['purchase_date'])
        ->where('total', $row['total'] ?? 0);

    // Invoice number
    if (($row['invoice_number'] ?? null) === null) {
        $existingQuery->whereNull('invoice_number');
    } else {
        $existingQuery->where(
            'invoice_number',
            $row['invoice_number']
        );
    }

    // Notes
    if (($row['notes'] ?? null) === null) {
        $existingQuery->whereNull('notes');
    } else {
        $existingQuery->where(
            'notes',
            $row['notes']
        );
    }

    // Created at agar restore ZIP yang sama tidak menggandakan data
    if (($row['created_at'] ?? null) === null) {
        $existingQuery->whereNull('created_at');
    } else {
        $existingQuery->where(
            'created_at',
            $row['created_at']
        );
    }

    $existing = $existingQuery->first();

    if ($existing) {
        $purchaseMap[$oldId] = $existing->id;
        continue;
    }

    $newId = DB::table('purchases')->insertGetId([
        'store_id' => $newStoreId,
        'supplier_id' => $newSupplierId,
        'user_id' => $userMap[(int) ($row['user_id'] ?? 0)] ?? $userId,
        'invoice_number' => $row['invoice_number'] ?? null,
        'purchase_date' => $row['purchase_date'],
        'total' => $row['total'] ?? 0,
        'notes' => $row['notes'] ?? null,
        'created_at' => $row['created_at'] ?? now(),
        'updated_at' => now(),
    ]);

    $purchaseMap[$oldId] = $newId;
}


            /*
            |--------------------------------------------------------------------------
            | 10. PURCHASE ITEMS
            |--------------------------------------------------------------------------
            */

            foreach ($purchaseItems as $row) {
    $oldPurchaseId = (int) ($row['purchase_id'] ?? 0);
    $oldProductId = (int) ($row['product_id'] ?? 0);

    if (
        !isset($purchaseMap[$oldPurchaseId]) ||
        !isset($productMap[$oldProductId])
    ) {
        continue;
    }

    $newPurchaseId = $purchaseMap[$oldPurchaseId];
    $newProductId = $productMap[$oldProductId];

    $existing = DB::table('purchase_items')
        ->where('purchase_id', $newPurchaseId)
        ->where('product_id', $newProductId)
        ->where('product_name', $row['product_name'])
        ->where('capital_price', $row['capital_price'] ?? 0)
        ->where('quantity', $row['quantity'] ?? 1)
        ->where('subtotal', $row['subtotal'] ?? 0)
        ->where('created_at', $row['created_at'] ?? now())
        ->first();

    if ($existing) {
        continue;
    }

    DB::table('purchase_items')->insert([
        'purchase_id' => $newPurchaseId,
        'product_id' => $newProductId,
        'product_name' => $row['product_name'],
        'sku' => $row['sku'] ?? null,
        'capital_price' => $row['capital_price'] ?? 0,
        'quantity' => $row['quantity'] ?? 1,
        'subtotal' => $row['subtotal'] ?? 0,
        'created_at' => $row['created_at'] ?? now(),
        'updated_at' => now(),
    ]);
}


            /*
            |--------------------------------------------------------------------------
            | 11. TRANSACTIONS
            |--------------------------------------------------------------------------
            */

            foreach ($transactions as $row) {

                $oldId = (int) $row['id'];
                $oldStoreId = (int) $row['store_id'];

                if (!isset($storeMap[$oldStoreId])) {
                    continue;
                }

                $existing = DB::table('transactions')
    ->where('store_id', $storeMap[$oldStoreId])
    ->where('invoice_number', $row['invoice_number'])
    ->first();

                if ($existing) {

                    $transactionMap[$oldId] = $existing->id;

                    continue;
                }

                $newId = DB::table('transactions')->insertGetId([
                    'invoice_number' => $row['invoice_number'],
                    'user_id' => $userMap[(int) ($row['user_id'] ?? 0)] ?? $userId,
                    'subtotal' => $row['subtotal'] ?? 0,
                    'discount' => $row['discount'] ?? 0,
                    'total' => $row['total'] ?? 0,
                    'paid' => $row['paid'] ?? 0,
                    'change' => $row['change'] ?? 0,
                    'payment_method' => $row['payment_method'] ?? null,
                    'created_at' => $row['created_at'] ?? now(),
                    'updated_at' => now(),
                    'store_id' => $storeMap[$oldStoreId],
                ]);

                $transactionMap[$oldId] = $newId;
            }


            /*
            |--------------------------------------------------------------------------
            | 12. TRANSACTION ITEMS
            |--------------------------------------------------------------------------
            */

            foreach ($transactionItems as $row) {
    $oldTransactionId = (int) ($row['transaction_id'] ?? 0);
    $oldProductId = (int) ($row['product_id'] ?? 0);

    if (
        !isset($transactionMap[$oldTransactionId]) ||
        !isset($productMap[$oldProductId])
    ) {
        continue;
    }

    $newTransactionId = $transactionMap[$oldTransactionId];
    $newProductId = $productMap[$oldProductId];

    $existingQuery = DB::table('transaction_items')
        ->where('transaction_id', $newTransactionId)
        ->where('product_id', $newProductId)
        ->where('product_name', $row['product_name'])
        ->where('capital_price', $row['capital_price'] ?? 0)
        ->where('price', $row['price'] ?? 0)
        ->where('quantity', $row['quantity'] ?? 1)
        ->where('subtotal', $row['subtotal'] ?? 0);

    // SKU
    if (($row['sku'] ?? null) === null) {
        $existingQuery->whereNull('sku');
    } else {
        $existingQuery->where('sku', $row['sku']);
    }

    // created_at dipakai sebagai identitas tambahan
    if (($row['created_at'] ?? null) === null) {
        $existingQuery->whereNull('created_at');
    } else {
        $existingQuery->where('created_at', $row['created_at']);
    }

    $existing = $existingQuery->first();

    if ($existing) {
        continue;
    }

    DB::table('transaction_items')->insert([
        'transaction_id' => $newTransactionId,
        'product_id' => $newProductId,
        'product_name' => $row['product_name'],
        'sku' => $row['sku'] ?? null,
        'capital_price' => $row['capital_price'] ?? 0,
        'price' => $row['price'] ?? 0,
        'quantity' => $row['quantity'] ?? 1,
        'subtotal' => $row['subtotal'] ?? 0,
        'created_at' => $row['created_at'] ?? now(),
        'updated_at' => now(),
    ]);
}


            /*
            |--------------------------------------------------------------------------
            | 13. EXPENSES
            |--------------------------------------------------------------------------
            */

            foreach ($expenses as $row) {

    $oldStoreId = $row['store_id'] !== null
        ? (int) $row['store_id']
        : null;

    if (!$oldStoreId || !isset($storeMap[$oldStoreId])) {
        continue;
    }

    $newStoreId = $storeMap[$oldStoreId];

    $existingQuery = DB::table('expenses')
        ->where('store_id', $newStoreId)
        ->where('category', $row['category'])
        ->where('amount', $row['amount'] ?? 0)
        ->where('expense_date', $row['expense_date']);

    // Description
    if (($row['description'] ?? null) === null) {
        $existingQuery->whereNull('description');
    } else {
        $existingQuery->where(
            'description',
            $row['description']
        );
    }

    // Created at
    if (($row['created_at'] ?? null) === null) {
        $existingQuery->whereNull('created_at');
    } else {
        $existingQuery->where(
            'created_at',
            $row['created_at']
        );
    }

    $existing = $existingQuery->first();

    if ($existing) {
        continue;
    }

    DB::table('expenses')->insert([
        'user_id' => $userMap[(int) ($row['user_id'] ?? 0)] ?? $userId,
        'category' => $row['category'],
        'description' => $row['description'] ?? null,
        'amount' => $row['amount'] ?? 0,
        'expense_date' => $row['expense_date'],
        'created_at' => $row['created_at'] ?? now(),
        'updated_at' => now(),
        'store_id' => $newStoreId,
    ]);
}


            /*
            |--------------------------------------------------------------------------
            | 14. SETTINGS
            |--------------------------------------------------------------------------
            */

            foreach ($settings as $row) {

                $oldStoreId = $row['store_id'] !== null
                    ? (int) $row['store_id']
                    : null;

                if (!$oldStoreId || !isset($storeMap[$oldStoreId])) {
                    continue;
                }

                $newStoreId = $storeMap[$oldStoreId];

                $existing = DB::table('settings')
                    ->where('store_id', $newStoreId)
                    ->first();

                $settingData = [
                    'store_name' => $row['store_name'] ?? 'KasirKU',
                    'store_address' => $row['store_address'] ?? null,
                    'store_phone' => $row['store_phone'] ?? null,
                    'store_email' => $row['store_email'] ?? null,
                    'currency' => $row['currency'] ?? 'IDR',
                    'allow_discount' => $row['allow_discount'] ?? 1,
                    'max_discount' => $row['max_discount'] ?? 100,
                    'minimum_stock' => $row['minimum_stock'] ?? 5,
                    'receipt_footer' => $row['receipt_footer'] ?? null,
                    'show_cashier' => $row['show_cashier'] ?? 1,
                    'show_payment_method' => $row['show_payment_method'] ?? 1,
                    'show_discount' => $row['show_discount'] ?? 1,
                    'updated_at' => now(),
                ];

                if ($existing) {

                    DB::table('settings')
                        ->where('id', $existing->id)
                        ->update($settingData);

                } else {

                    $settingData['store_id'] = $newStoreId;
                    $settingData['created_at'] = $row['created_at'] ?? now();

                    DB::table('settings')->insert($settingData);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | 15. TRANSFERS
            |--------------------------------------------------------------------------
            */

            foreach ($transfers as $row) {

    $oldId = (int) ($row['id'] ?? 0);

    $oldSource = (int) ($row['source_store_id'] ?? 0);
    $oldDestination = (int) ($row['destination_store_id'] ?? 0);

    if (
        !$oldId ||
        !isset($storeMap[$oldSource]) ||
        !isset($storeMap[$oldDestination])
    ) {
        continue;
    }

    $newSourceStoreId = $storeMap[$oldSource];
    $newDestinationStoreId = $storeMap[$oldDestination];

    $existingQuery = DB::table('transfers')
        ->where('source_store_id', $newSourceStoreId)
        ->where('destination_store_id', $newDestinationStoreId)
        ->where('transfer_date', $row['transfer_date'])
        ->where('status', $row['status'] ?? 'pending');

    // Notes
    if (($row['notes'] ?? null) === null) {
        $existingQuery->whereNull('notes');
    } else {
        $existingQuery->where('notes', $row['notes']);
    }

    // created_at sebagai identitas tambahan
    if (($row['created_at'] ?? null) === null) {
        $existingQuery->whereNull('created_at');
    } else {
        $existingQuery->where('created_at', $row['created_at']);
    }

    $existing = $existingQuery->first();

    if ($existing) {
        $transferMap[$oldId] = $existing->id;
        continue;
    }

    $newId = DB::table('transfers')->insertGetId([
        'source_store_id' => $newSourceStoreId,
        'destination_store_id' => $newDestinationStoreId,
        'user_id' => $userMap[(int) ($row['user_id'] ?? 0)] ?? $userId,
        'transfer_date' => $row['transfer_date'],
        'status' => $row['status'] ?? 'pending',
        'notes' => $row['notes'] ?? null,
        'created_at' => $row['created_at'] ?? now(),
        'updated_at' => now(),
    ]);

    $transferMap[$oldId] = $newId;
}


            /*
            |--------------------------------------------------------------------------
            | 16. TRANSFER ITEMS
            |--------------------------------------------------------------------------
            */

            foreach ($transferItems as $row) {

    $oldTransferId = (int) ($row['transfer_id'] ?? 0);
    $oldProductId = (int) ($row['product_id'] ?? 0);

    if (
        !isset($transferMap[$oldTransferId]) ||
        !isset($productMap[$oldProductId])
    ) {
        continue;
    }

    $newTransferId = $transferMap[$oldTransferId];
    $newProductId = $productMap[$oldProductId];

    $existingQuery = DB::table('transfer_items')
        ->where('transfer_id', $newTransferId)
        ->where('product_id', $newProductId)
        ->where('product_name', $row['product_name'])
        ->where('quantity', $row['quantity']);

    // SKU
    if (($row['sku'] ?? null) === null) {
        $existingQuery->whereNull('sku');
    } else {
        $existingQuery->where('sku', $row['sku']);
    }

    // created_at
    if (($row['created_at'] ?? null) === null) {
        $existingQuery->whereNull('created_at');
    } else {
        $existingQuery->where('created_at', $row['created_at']);
    }

    $existing = $existingQuery->first();

    if ($existing) {
        continue;
    }

    DB::table('transfer_items')->insert([
        'transfer_id' => $newTransferId,
        'product_id' => $newProductId,
        'product_name' => $row['product_name'],
        'sku' => $row['sku'] ?? null,
        'quantity' => $row['quantity'],
        'created_at' => $row['created_at'] ?? now(),
        'updated_at' => now(),
    ]);
}
        });


        /*
        |--------------------------------------------------------------------------
        | Hapus file sementara
        |--------------------------------------------------------------------------
        */

        File::delete($zipPath);

        session()->forget([
            'restore_backup_path',
            'restore_backup_metadata',
        ]);

        return redirect()
            ->route('backup.index')
            ->with(
                'success',
                'Restore data berhasil dilakukan.'
            );

    } catch (\Throwable $e) {

        if ($zip->status === ZipArchive::ER_OK) {
            $zip->close();
        }

        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        session()->forget([
            'restore_backup_path',
            'restore_backup_metadata',
        ]);

        report($e);

        return redirect()
            ->route('backup.restore')
            ->with(
                'error',
                'Restore gagal dan tidak ada perubahan database yang disimpan. ' .
                $e->getMessage()
            );
    }
}

    /**
     * Download backup seluruh data toko milik owner.
     */
    public function download(Request $request)
    {
        // Pastikan user sedang login.
        if (!session('logged_in')) {
            abort(403, 'Anda harus login.');
        }

        $userId = (int) session('user_id');

        // Ambil hanya toko yang benar-benar dimiliki user.
        $stores = Store::where('owner_id', $userId)->get();

        if ($stores->isEmpty()) {
            return back()->with('error', 'Anda belum memiliki toko.');
        }

        $storeIds = $stores->pluck('id')->map(fn ($id) => (int) $id)->values()->all();

        /*
        |--------------------------------------------------------------------------
        | Ambil data berdasarkan toko
        |--------------------------------------------------------------------------
        */

        $data = [];
$data['users'] = DB::table('users')
    ->whereIn('id', function ($query) use ($storeIds) {
        $query->select('user_id')
            ->from('store_user')
            ->whereIn('store_id', $storeIds);
    })
    ->get()
    ->map(function ($user) {
        return [
            'id' => $user->id,
            'username' => property_exists($user, 'username')
                ? $user->username
                : null,
            'email' => property_exists($user, 'email')
                ? $user->email
                : null,
            'name' => property_exists($user, 'name')
                ? $user->name
                : null,
            'role' => property_exists($user, 'role')
                ? $user->role
                : null,
        ];
    })
    ->values()
    ->all();

        // Toko
        $data['stores'] = DB::table('stores')
            ->whereIn('id', $storeIds)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        // User yang terhubung ke toko
        $data['store_users'] = DB::table('store_user')
            ->whereIn('store_id', $storeIds)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        // Produk
        $data['products'] = DB::table('products')
            ->whereIn('store_id', $storeIds)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        // Riwayat produk
        $data['product_histories'] = DB::table('product_histories')
            ->whereIn('store_id', $storeIds)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        // Pelanggan
        $data['customers'] = DB::table('customers')
            ->whereIn('store_id', $storeIds)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        // Hutang
        $data['debts'] = DB::table('debts')
            ->whereIn('store_id', $storeIds)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        // Pembayaran hutang
        $debtIds = collect($data['debts'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $data['debt_payments'] = empty($debtIds)
            ? []
            : DB::table('debt_payments')
                ->whereIn('debt_id', $debtIds)
                ->get()
                ->map(fn ($row) => (array) $row)
                ->values()
                ->all();

        // Supplier
        $data['suppliers'] = DB::table('suppliers')
            ->whereIn('store_id', $storeIds)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        // Pembelian
        $data['purchases'] = DB::table('purchases')
            ->whereIn('store_id', $storeIds)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        // Item pembelian
        $purchaseIds = collect($data['purchases'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $data['purchase_items'] = empty($purchaseIds)
            ? []
            : DB::table('purchase_items')
                ->whereIn('purchase_id', $purchaseIds)
                ->get()
                ->map(fn ($row) => (array) $row)
                ->values()
                ->all();

        // Transaksi
        $data['transactions'] = DB::table('transactions')
            ->whereIn('store_id', $storeIds)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        // Item transaksi
        $transactionIds = collect($data['transactions'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $data['transaction_items'] = empty($transactionIds)
            ? []
            : DB::table('transaction_items')
                ->whereIn('transaction_id', $transactionIds)
                ->get()
                ->map(fn ($row) => (array) $row)
                ->values()
                ->all();

        // Pengeluaran
        $data['expenses'] = DB::table('expenses')
            ->whereIn('store_id', $storeIds)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        // Pengaturan toko
        $data['settings'] = DB::table('settings')
            ->whereIn('store_id', $storeIds)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Transfer antar toko
        |--------------------------------------------------------------------------
        |
        | Transfer bisa memiliki source_store_id dan destination_store_id.
        | Kita ambil transfer jika salah satunya merupakan toko milik owner.
        |
        */

        $data['transfers'] = DB::table('transfers')
            ->where(function ($query) use ($storeIds) {
                $query
                    ->whereIn('source_store_id', $storeIds)
                    ->orWhereIn('destination_store_id', $storeIds);
            })
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values()
            ->all();

        // Item transfer
        $transferIds = collect($data['transfers'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $data['transfer_items'] = empty($transferIds)
            ? []
            : DB::table('transfer_items')
                ->whereIn('transfer_id', $transferIds)
                ->get()
                ->map(fn ($row) => (array) $row)
                ->values()
                ->all();

        /*
        |--------------------------------------------------------------------------
        | Metadata backup
        |--------------------------------------------------------------------------
        */

        $backup = [
            'application' => 'KasirKU',
            'format_version' => '1.0',
            'backup_date' => now()->format('Y-m-d H:i:s'),
            'owner_id' => $userId,
            'store_ids' => $storeIds,
            'store_count' => count($storeIds),
            'tables' => array_keys($data),
        ];

        /*
        |--------------------------------------------------------------------------
        | Buat folder sementara
        |--------------------------------------------------------------------------
        */

        $backupId = 'kasirku-backup-' . now()->format('Ymd-His') . '-' . uniqid();

        $backupPath = storage_path('app/' . $backupId);
        $zipPath = storage_path('app/' . $backupId . '.zip');

        File::makeDirectory($backupPath, 0755, true);

        /*
        |--------------------------------------------------------------------------
        | Simpan metadata
        |--------------------------------------------------------------------------
        */

        File::put(
            $backupPath . '/backup.json',
            json_encode(
                $backup,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Simpan setiap tabel sebagai JSON
        |--------------------------------------------------------------------------
        */

        foreach ($data as $table => $rows) {
            File::put(
                $backupPath . '/' . $table . '.json',
                json_encode(
                    $rows,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Buat ZIP
        |--------------------------------------------------------------------------
        */

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            File::deleteDirectory($backupPath);

            return back()->with(
                'error',
                'Backup gagal dibuat.'
            );
        }

        foreach (File::allFiles($backupPath) as $file) {
            $zip->addFile(
                $file->getRealPath(),
                'kasirku-backup/' . $file->getFilename()
            );
        }

        $zip->close();

        /*
        |--------------------------------------------------------------------------
        | Hapus folder sementara
        |--------------------------------------------------------------------------
        */

        File::deleteDirectory($backupPath);

        /*
        |--------------------------------------------------------------------------
        | Download ZIP
        |--------------------------------------------------------------------------
        */

        return response()
            ->download(
                $zipPath,
                'kasirku-backup-' . now()->format('Y-m-d-His') . '.zip'
            )
            ->deleteFileAfterSend(true);
    }
}