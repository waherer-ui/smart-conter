<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Store;
use App\Models\Transfer;
use App\Models\TransferItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /**
     * Mendapatkan ID toko aktif.
     */
    private function activeStoreId(): int
    {
        return (int) session('active_store_id');
    }

    /**
     * Daftar transfer toko aktif.
     */
    public function index()
    {
        $storeId = $this->activeStoreId();

        $transfers = Transfer::with([
            'sourceStore',
            'destinationStore',
            'user',
            'items',
        ])
            ->where(function ($query) use ($storeId) {
                $query->where('source_store_id', $storeId)
                    ->orWhere('destination_store_id', $storeId);
            })
            ->latest()
            ->paginate(15);

        return view('purchases.transfer-index', compact('transfers'));
    }

    /**
     * Form transfer antar toko.
     */
    public function create()
{
    $storeId = $this->activeStoreId();

    $currentStore = Store::findOrFail($storeId);

    $stores = Store::where('owner_id', $currentStore->owner_id)
        ->where('id', '!=', $storeId)
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    $products = $currentStore->products()
        ->orderBy('name')
        ->get();

    return view('purchases.transfer', compact(
        'currentStore',
        'stores',
        'products'
    ));
}
    
    /**
 * Detail transfer.
 */
public function show(int $id)
{
    $storeId = $this->activeStoreId();

    $transfer = Transfer::with([
        'sourceStore',
        'destinationStore',
        'user',
        'items.product',
    ])
        ->where(function ($query) use ($storeId) {
            $query->where('source_store_id', $storeId)
                ->orWhere('destination_store_id', $storeId);
        })
        ->findOrFail($id);

    return view('purchases.transfer-show', compact('transfer'));
}

public function receive(int $id)
{
    $storeId = $this->activeStoreId();

    $transfer = Transfer::with([
        'sourceStore',
        'destinationStore',
        'items',
    ])
        ->where('destination_store_id', $storeId)
        ->where('status', 'pending')
        ->findOrFail($id);

    DB::transaction(function () use ($transfer, $storeId) {
        // Kunci transfer agar tidak bisa diterima dua kali
        $lockedTransfer = Transfer::where('id', $transfer->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($lockedTransfer->status !== 'pending') {
            throw new \RuntimeException(
                'Transfer ini sudah tidak menunggu penerimaan.'
            );
        }

        foreach ($lockedTransfer->items as $item) {

            // Produk asal masih diperlukan untuk mengambil metadata
            // jika produk dengan SKU tersebut belum ada di toko tujuan.
            $sourceProduct = Product::find($item->product_id);

            if (!$sourceProduct) {
                throw new \RuntimeException(
                    "Produk sumber untuk SKU {$item->sku} tidak ditemukan."
                );
            }

            // Cari produk berdasarkan SKU di toko tujuan.
            $destinationProduct = Product::where('store_id', $storeId)
                ->where('sku', $item->sku)
                ->lockForUpdate()
                ->first();

            // Jika produk belum ada di toko tujuan,
            // buat produk baru berdasarkan data produk sumber.
            if (!$destinationProduct) {
                $destinationProduct = Product::create([
                    'store_id' => $storeId,
                    'sku' => $sourceProduct->sku,
                    'name' => $sourceProduct->name,
                    'category' => $sourceProduct->category,
                    'brand' => $sourceProduct->brand,
                    'capital_price' => $sourceProduct->capital_price,
                    'price' => $sourceProduct->price,
                    'stock' => 0,
                    'image' => $sourceProduct->image,
                ]);
            }

            $stockBefore = (int) $destinationProduct->stock;
            $quantity = (int) $item->quantity;
            $stockAfter = $stockBefore + $quantity;

            $destinationProduct->increment('stock', $quantity);

            ProductHistory::create([
                'store_id' => $storeId,
                'product_id' => $destinationProduct->id,
                'sku' => $destinationProduct->sku,
                'name' => $destinationProduct->name,
                'added_stock' => $quantity,
                'status_type' => sprintf(
                    'Transfer Masuk | %s → %s | Stok: %d → %d',
                    $transfer->sourceStore->name,
                    $transfer->destinationStore->name,
                    $stockBefore,
                    $stockAfter
                ),
            ]);
        }

        $lockedTransfer->update([
            'status' => 'completed',
        ]);
    });

    return redirect()
        ->route('transfer.show', $transfer->id)
        ->with(
            'success',
            'Transfer berhasil diterima. Stok toko tujuan telah bertambah.'
        );
}

    /**
     * Simpan transfer dari toko aktif ke toko tujuan.
     */
    public function store(Request $request)
    {
        $storeId = $this->activeStoreId();

        $validated = $request->validate([
            'destination_store_id' => [
                'required',
                'integer',
            ],
            'transfer_date' => [
                'required',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.product_id' => [
                'required',
                'integer',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $sourceStore = Store::findOrFail($storeId);

        /*
         * Pastikan toko tujuan benar-benar milik
         * owner yang sama.
         */
        $destinationStore = Store::where('id', $validated['destination_store_id'])
            ->where('owner_id', $sourceStore->owner_id)
            ->where('is_active', true)
            ->firstOrFail();

        if ($destinationStore->id === $sourceStore->id) {
            return back()
                ->withErrors([
                    'destination_store_id' => 'Toko tujuan harus berbeda dari toko asal.',
                ])
                ->withInput();
        }

        $transfer = DB::transaction(function () use (
            $validated,
            $sourceStore,
            $destinationStore,
            $storeId
        ) {
            $transfer = Transfer::create([
                'source_store_id' => $sourceStore->id,
                'destination_store_id' => $destinationStore->id,
                'user_id' => session('user_id'),
                'transfer_date' => $validated['transfer_date'],
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                /*
                 * Produk HARUS berasal dari toko aktif.
                 */
                $product = $sourceStore->products()
                    ->where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new \RuntimeException(
                        'Produk transfer tidak ditemukan di toko asal.'
                    );
                }

                $quantity = (int) $item['quantity'];
                $stockBefore = (int) $product->stock;

                if ($quantity > $stockBefore) {
                    throw new \RuntimeException(
                        "Stok produk {$product->name} tidak mencukupi."
                    );
                }

                $stockAfter = $stockBefore - $quantity;

                TransferItem::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $quantity,
                ]);

                /*
                 * Kurangi stok toko asal.
                 */
                $product->decrement('stock', $quantity);

                /*
                 * Catat stok keluar.
                 *
                 * ProductHistory tidak memiliki user_id
                 * atau description pada database saat ini,
                 * jadi kita hanya menggunakan kolom yang tersedia.
                 */
                ProductHistory::create([
                    'store_id' => $sourceStore->id,
                    'product_id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'added_stock' => -$quantity,
                    'status_type' => sprintf(
                        'Transfer Keluar | %s → %s | Stok: %d → %d',
                        $sourceStore->name,
                        $destinationStore->name,
                        $stockBefore,
                        $stockAfter
                    ),
                ]);
            }

            return $transfer;
        });

        return redirect()
            ->route('transfer.index')
            ->with(
                'success',
                "Transfer berhasil dibuat dan menunggu penerimaan di {$destinationStore->name}."
            );
    }
}