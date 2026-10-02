<?php

namespace App\Http\Controllers;

use App\Models\PriceRule;
use App\Models\Product;
use Illuminate\Http\Request;

class PriceRuleController extends Controller
{
    private function activeStoreId()
    {
        return session('active_store_id');
    }

    public function index()
    {
        $storeId = $this->activeStoreId();

        $priceRules = PriceRule::with('product')
            ->where('store_id', $storeId)
            ->latest()
            ->get();

        $products = Product::where('store_id', $storeId)
            ->orderBy('name')
            ->get();

        return view('price-rules.index', compact(
            'priceRules',
            'products'
        ));
    }

    public function store(Request $request)
    {
        $storeId = $this->activeStoreId();

        $request->validate([
            'type' => 'required|in:reseller,promotion',

            'product_id' => 'nullable|exists:products,id',

            'min_quantity' => 'nullable|integer|min:1',

            'discount_type' => 'nullable|in:nominal,percent',

            'discount_value' => 'nullable|numeric|min:0',

            'special_price' => 'nullable|numeric|min:0',

            'start_at' => 'nullable|date',

            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Pastikan produk berasal dari toko aktif
        |--------------------------------------------------------------------------
        */
        if ($request->product_id) {

            $productExists = Product::where('id', $request->product_id)
                ->where('store_id', $storeId)
                ->exists();

            if (!$productExists) {
                return back()
                    ->withInput()
                    ->with('error', 'Produk tidak ditemukan.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RESELLER / GROSIR
        |--------------------------------------------------------------------------
        */
        if ($request->type === 'reseller') {

            if (!$request->min_quantity) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Minimal quantity reseller wajib diisi.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Produk tertentu
            |
            | Gunakan harga khusus tetap.
            |--------------------------------------------------------------------------
            */
            if ($request->product_id) {

                if ($request->special_price === null) {
                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'Harga khusus reseller wajib diisi.'
                        );
                }

                $request->merge([
                    'discount_type' => null,
                    'discount_value' => null,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Semua produk
            |
            | Gunakan potongan per produk:
            | Rp atau %
            |--------------------------------------------------------------------------
            */
            else {

                if (
                    !$request->discount_type ||
                    $request->discount_value === null
                ) {
                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'Jenis dan nilai potongan reseller wajib diisi.'
                        );
                }

                if (
                    $request->discount_type === 'percent' &&
                    $request->discount_value > 100
                ) {
                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'Potongan persentase tidak boleh lebih dari 100%.'
                        );
                }

                $request->merge([
                    'special_price' => null,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PROMOSI
        |--------------------------------------------------------------------------
        */
        if ($request->type === 'promotion') {

            if (
                !$request->discount_type ||
                $request->discount_value === null
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Jenis dan nilai diskon promosi wajib diisi.'
                    );
            }

            if (
                $request->discount_type === 'percent' &&
                $request->discount_value > 100
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Diskon persentase tidak boleh lebih dari 100%.'
                    );
            }

            /*
            | Promosi tidak memakai harga khusus reseller
            */
            $request->merge([
                'special_price' => null,
                'min_quantity' => null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Nonaktifkan aturan aktif sebelumnya pada scope yang sama
        |--------------------------------------------------------------------------
        */
        PriceRule::where('store_id', $storeId)
            ->where('is_active', true)
            ->where(function ($query) use ($request) {

                if ($request->product_id) {

                    $query->where(
                        'product_id',
                        $request->product_id
                    );

                } else {

                    $query->whereNull('product_id');

                }

            })
            ->update([
                'is_active' => false,
            ]);

        /*
        |--------------------------------------------------------------------------
        | Simpan aturan
        |--------------------------------------------------------------------------
        */
        PriceRule::create([
            'store_id' => $storeId,

            'product_id' => $request->product_id,

            'type' => $request->type,

            'min_quantity' => $request->min_quantity,

            'discount_type' => $request->discount_type,

            'discount_value' => $request->discount_value,

            'special_price' => $request->special_price,

            'start_at' => $request->start_at,

            'end_at' => $request->end_at,

            'is_active' => true,
        ]);

        return back()->with(
            'success',
            'Aturan harga berhasil ditambahkan dan diaktifkan.'
        );
    }

    public function toggle(PriceRule $priceRule)
    {
        $this->authorizeStore($priceRule);

        if (!$priceRule->is_active) {

            PriceRule::where('store_id', $priceRule->store_id)
                ->where('id', '!=', $priceRule->id)
                ->where('is_active', true)
                ->where(function ($query) use ($priceRule) {

                    if ($priceRule->product_id) {

                        $query->where(
                            'product_id',
                            $priceRule->product_id
                        );

                    } else {

                        $query->whereNull('product_id');

                    }

                })
                ->update([
                    'is_active' => false,
                ]);
        }

        $priceRule->update([
            'is_active' => !$priceRule->is_active,
        ]);

        return back()->with(
            'success',
            'Status aturan harga berhasil diperbarui.'
        );
    }

    public function destroy(PriceRule $priceRule)
    {
        $this->authorizeStore($priceRule);

        $priceRule->delete();

        return back()->with(
            'success',
            'Aturan harga berhasil dihapus.'
        );
    }

    private function authorizeStore(PriceRule $priceRule): void
    {
        abort_unless(
            $priceRule->store_id == $this->activeStoreId(),
            403
        );
    }
}