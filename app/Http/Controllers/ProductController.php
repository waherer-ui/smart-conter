<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR PRODUK
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $category = $request->input('category');
        $search = $request->input('search');

        $dateFilter = $request->input('date_filter', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        /*
        |--------------------------------------------------------------------------
        | QUERY PRODUK
        |--------------------------------------------------------------------------
        */

        $query = Product::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $products = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Product::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        /*
        |--------------------------------------------------------------------------
        | RIWAYAT PRODUK
        |--------------------------------------------------------------------------
        */

        $historyQuery = ProductHistory::orderBy(
            'created_at',
            'desc'
        );

        if ($dateFilter === 'today') {

            $historyQuery->whereDate(
                'created_at',
                today()
            );

        } elseif ($dateFilter === '7days') {

            $historyQuery->where(
                'created_at',
                '>=',
                now()->subDays(7)
            );

        } elseif ($dateFilter === '30days') {

            $historyQuery->where(
                'created_at',
                '>=',
                now()->subDays(30)
            );

        } elseif ($dateFilter === '3months') {

            $historyQuery->where(
                'created_at',
                '>=',
                now()->subMonths(3)
            );

        } elseif ($dateFilter === '1year') {

            $historyQuery->where(
                'created_at',
                '>=',
                now()->subYear()
            );

        } elseif ($dateFilter === '3years') {

            $historyQuery->where(
                'created_at',
                '>=',
                now()->subYears(3)
            );

        } elseif ($dateFilter === '5years') {

            $historyQuery->where(
                'created_at',
                '>=',
                now()->subYears(5)
            );

        } elseif (
            $dateFilter === 'custom'
            && $startDate
            && $endDate
        ) {

            $historyQuery->whereBetween(
                'created_at',
                [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ]
            );
        }

        $historyGroups = $historyQuery
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view(
            'produk',
            compact(
                'products',
                'categories',
                'category',
                'search',
                'historyGroups',
                'dateFilter',
                'startDate',
                'endDate'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS RIWAYAT
    |--------------------------------------------------------------------------
    */

    public function destroyHistory($id)
    {
        $history = ProductHistory::findOrFail($id);

        $product = Product::find(
            $history->product_id
        );

        /*
        |--------------------------------------------------------------------------
        | Sesuaikan Stok
        |--------------------------------------------------------------------------
        */

        if ($product && $history->added_stock > 0) {

            $product->stock -= $history->added_stock;

            if ($product->stock < 0) {
                $product->stock = 0;
            }

            $product->save();
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus Riwayat
        |--------------------------------------------------------------------------
        */

        $history->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'Riwayat penambahan berhasil dihapus dan stok disesuaikan!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function dashboard(Request $request)
    {
        $category = $request->input('category');

        if ($category && $category !== 'all') {

            $products = Product::where(
                'category',
                $category
            )->get();

        } else {

            $products = Product::all();
        }

        $categories = Product::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view(
            'home',
            compact(
                'products',
                'categories',
                'category'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | KASIR / POS
    |--------------------------------------------------------------------------
    */

    public function kasir(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $query = Product::query();

        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'category',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'sku',
                    'like',
                    '%' . $search . '%'
                );

            });
        }

        if ($category && $category !== 'all') {

            $query->where(
                'category',
                $category
            );
        }

        $products = $query
            ->orderBy('name')
            ->get();

        $categories = Product::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view(
            'kasir',
            compact(
                'products',
                'categories',
                'category',
                'search'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH PRODUK / RESTOCK
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'category' => [
                'required',
                'string',
                'max:255'
            ],

            'brand' => [
                'nullable',
                'string',
                'max:255'
            ],

            'capital_price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'stock' => [
                'required',
                'integer',
                'min:0'
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048'
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | Informasi Petugas
        |--------------------------------------------------------------------------
        */

        $username = session(
            'username',
            'Tidak diketahui'
        );

        $role = session(
            'user_role',
            'tidak diketahui'
        );

        $petugas =
            ucfirst($role) .
            ': ' .
            $username;


        /*
        |--------------------------------------------------------------------------
        | UPLOAD GAMBAR
        |--------------------------------------------------------------------------
        |
        | Semua gambar menggunakan disk product_images.
        |
        */

                $imagePath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            $file->move(public_path('products'), $filename);
            
            $imagePath = $filename;
        }



        /*
        |--------------------------------------------------------------------------
        | CEK PRODUK YANG SAMA
        |--------------------------------------------------------------------------
        */

        $existingProduct = Product::where(
            'name',
            trim($request->name)
        )
        ->where(
            'category',
            trim($request->category)
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | JIKA PRODUK SUDAH ADA = RESTOCK
        |--------------------------------------------------------------------------
        */

        if ($existingProduct) {

            $stockBefore =
                $existingProduct->stock;

            $addedStock =
                (int) $request->stock;

            $stockAfter =
                $stockBefore + $addedStock;


            /*
            |--------------------------------------------------------------------------
            | Update Produk
            |--------------------------------------------------------------------------
            */

            $existingProduct->stock =
                $stockAfter;

            $existingProduct->capital_price =
                $request->capital_price;

            $existingProduct->price =
                $request->price;

            if ($request->filled('brand')) {

                $existingProduct->brand =
                    $request->brand;
            }


            /*
            /*
            |--------------------------------------------------------------------------
            | Update Gambar
            |--------------------------------------------------------------------------
            */

            if ($imagePath) {

                if (
                    $existingProduct->image &&
                    file_exists(public_path('products/' . $existingProduct->image))
                ) {
                    @unlink(public_path('products/' . $existingProduct->image));
                }

                $existingProduct->image =
                    $imagePath;
            }

            $existingProduct->save();



            /*
            |--------------------------------------------------------------------------
            | Simpan Riwayat Restock
            |--------------------------------------------------------------------------
            */

            ProductHistory::create([

                'product_id' =>
                    $existingProduct->id,

                'sku' =>
                    $existingProduct->sku,

                'name' =>
                    $existingProduct->name,

                'added_stock' =>
                    $addedStock,

                'status_type' =>
                    'Restock | ' .
                    $petugas .
                    ' | Stok: ' .
                    $stockBefore .
                    ' → ' .
                    $stockAfter,

            ]);

            return redirect()
                ->back()
                ->with(
                    'success',
                    'Stok produk "' .
                    $existingProduct->name .
                    '" berhasil direstock!'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | PRODUK BARU
        |--------------------------------------------------------------------------
        */

        $prefix = strtoupper(
            substr(
                preg_replace(
                    '/[^a-zA-Z]/',
                    '',
                    $request->category
                ),
                0,
                3
            )
        );

        if (empty($prefix)) {
            $prefix = 'PRD';
        }


        /*
        |--------------------------------------------------------------------------
        | Generate SKU
        |--------------------------------------------------------------------------
        */

        $count = Product::where(
            'category',
            $request->category
        )->count();

        $nextNumber = $count + 1;

        $sku =
            $prefix .
            '-' .
            str_pad(
                $nextNumber,
                3,
                '0',
                STR_PAD_LEFT
            );


        /*
        |--------------------------------------------------------------------------
        | Buat Produk
        |--------------------------------------------------------------------------
        */

        $product = Product::create([

            'sku' =>
                $sku,

            'name' =>
                $request->name,

            'category' =>
                $request->category,

            'brand' =>
                $request->brand,

            'capital_price' =>
                $request->capital_price,

            'price' =>
                $request->price,

            'stock' =>
                $request->stock,

            'image' =>
                $imagePath,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Simpan Riwayat Produk Baru
        |--------------------------------------------------------------------------
        */

        ProductHistory::create([

            'product_id' =>
                $product->id,

            'sku' =>
                $sku,

            'name' =>
                $request->name,

            'added_stock' =>
                $request->stock,

            'status_type' =>
                'Produk Baru | ' .
                $petugas .
                ' | Stok Awal: ' .
                $request->stock,

        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Produk baru berhasil ditambahkan dengan SKU: ' .
                $sku
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT PRODUK
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'category' => [
                'required',
                'string',
                'max:255'
            ],

            'brand' => [
                'nullable',
                'string',
                'max:255'
            ],

            'capital_price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'stock' => [
                'required',
                'integer',
                'min:0'
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048'
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Cari Produk
        |--------------------------------------------------------------------------
        */

        $product =
            Product::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Simpan Data Lama
        |--------------------------------------------------------------------------
        */

        $oldName =
            $product->name;

        $oldCategory =
            $product->category;

        $oldBrand =
            $product->brand;

        $oldCapitalPrice =
            $product->capital_price;

        $oldPrice =
            $product->price;

        $oldStock =
            $product->stock;

        $oldImage =
            $product->image;


        /*
        |--------------------------------------------------------------------------
        | Petugas
        |--------------------------------------------------------------------------
        */

        $username =
            session(
                'username',
                'Tidak diketahui'
            );

        $role =
            session(
                'user_role',
                'tidak diketahui'
            );

        $petugas =
            ucfirst($role) .
            ': ' .
            $username;


        /*
        |--------------------------------------------------------------------------
        | GANTI GAMBAR
        |--------------------------------------------------------------------------
        */

        $imagePath =
            $product->image;

                if ($request->hasFile('image')) {

            /*
            |--------------------------------------------------------------------------
            | Hapus gambar lama
            |--------------------------------------------------------------------------
            */

            if ($imagePath && file_exists(public_path('products/' . $imagePath))) {
                @unlink(public_path('products/' . $imagePath));
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan gambar baru ke public/products
            |--------------------------------------------------------------------------
            */

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            $file->move(public_path('products'), $filename);
            
            $imagePath = $filename;
        }



        /*
        |--------------------------------------------------------------------------
        | Update Produk
        |--------------------------------------------------------------------------
        */

        $product->update([

            'name' =>
                $request->name,

            'category' =>
                $request->category,

            'brand' =>
                $request->brand,

            'capital_price' =>
                $request->capital_price,

            'price' =>
                $request->price,

            'stock' =>
                $request->stock,

            'image' =>
                $imagePath,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Catat Perubahan
        |--------------------------------------------------------------------------
        */

        $changes = [];


        if ($oldName != $product->name) {

            $changes[] =
                'Nama: ' .
                $oldName .
                ' → ' .
                $product->name;
        }


        if ($oldCategory != $product->category) {

            $changes[] =
                'Kategori: ' .
                $oldCategory .
                ' → ' .
                $product->category;
        }


        if ($oldBrand != $product->brand) {

            $changes[] =
                'Brand: ' .
                ($oldBrand ?: '-') .
                ' → ' .
                ($product->brand ?: '-');
        }


        if ($oldCapitalPrice != $product->capital_price) {

            $changes[] =
                'Modal: ' .
                $oldCapitalPrice .
                ' → ' .
                $product->capital_price;
        }


        if ($oldPrice != $product->price) {

            $changes[] =
                'Harga: ' .
                $oldPrice .
                ' → ' .
                $product->price;
        }


        if ($oldStock != $product->stock) {

            $changes[] =
                'Stok: ' .
                $oldStock .
                ' → ' .
                $product->stock;
        }


        if ($oldImage != $product->image) {

            $changes[] =
                'Foto produk diperbarui';
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan Riwayat Jika Ada Perubahan
        |--------------------------------------------------------------------------
        */

        if (!empty($changes)) {

            ProductHistory::create([

                'product_id' =>
                    $product->id,

                'sku' =>
                    $product->sku,

                'name' =>
                    $product->name,

                'added_stock' =>
                    0,

                'status_type' =>
                    'Edit Produk | ' .
                    $petugas .
                    ' | ' .
                    implode(
                        ' ; ',
                        $changes
                    ),

            ]);
        }


        return redirect()
            ->back()
            ->with(
                'success',
                'Data produk berhasil diperbarui!'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS PRODUK
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $product =
            Product::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Simpan Identitas Produk
        |--------------------------------------------------------------------------
        */

        $sku =
            $product->sku;

        $name =
            $product->name;

        $stock =
            $product->stock;

        $image =
            $product->image;


        /*
        |--------------------------------------------------------------------------
        | Petugas
        |--------------------------------------------------------------------------
        */

        $username =
            session(
                'username',
                'Tidak diketahui'
            );

        $role =
            session(
                'user_role',
                'tidak diketahui'
            );

        $petugas =
            ucfirst($role) .
            ': ' .
            $username;


        /*
        |--------------------------------------------------------------------------
        | Simpan Riwayat Penghapusan
        |--------------------------------------------------------------------------
        */

        ProductHistory::create([

            'product_id' =>
                $product->id,

            'sku' =>
                $sku,

            'name' =>
                $name,

            'added_stock' =>
                0,

            'status_type' =>
                'Hapus Produk | ' .
                $petugas .
                ' | Stok terakhir: ' .
                $stock,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Hapus Gambar
        |--------------------------------------------------------------------------
        */

        if (
            $image &&
            Storage::disk('product_images')->exists(
                $image
            )
        ) {

            Storage::disk('product_images')->delete(
                $image
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Hapus Produk
        |--------------------------------------------------------------------------
        */

        $product->delete();


        return redirect()
            ->back()
            ->with(
                'success',
                'Produk "' .
                $name .
                '" berhasil dihapus!'
            );
    }
    /*
    |--------------------------------------------------------------------------
    | SCAN PRODUK BERDASARKAN SKU
    |--------------------------------------------------------------------------
    */

    public function scanBySku(string $sku)
    {
        $product = Product::where(
            'sku',
            $sku
        )->first();

        if (!$product) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Produk dengan SKU ' .
                    $sku .
                    ' tidak ditemukan.'
            ], 404);
        }

        if ($product->stock <= 0) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Stok produk ' .
                    $product->name .
                    ' habis.'
            ], 422);
        }

        return response()->json([

            'success' => true,

            'product' => [

                'id' =>
                    $product->id,

                'name' =>
                    $product->name,

                'sku' =>
                    $product->sku,

                'price' =>
                    (float) $product->price,

                'stock' =>
                    (int) $product->stock,

            ]

        ]);
    }
}
