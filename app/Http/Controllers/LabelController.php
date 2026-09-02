<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN CETAK LABEL
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Ambil ID produk yang dipilih
        |--------------------------------------------------------------------------
        */

        $selectedIds = $request->input('products', []);


        /*
        |--------------------------------------------------------------------------
        | Pastikan bentuknya array
        |--------------------------------------------------------------------------
        */

        if (!is_array($selectedIds)) {
            $selectedIds = [];
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil produk
        |--------------------------------------------------------------------------
        |
        | Jika belum ada produk yang dipilih,
        | tampilkan semua produk.
        |
        */

        $products = Product::query()
            ->when(
                !empty($selectedIds),
                function ($query) use ($selectedIds) {
                    $query->whereIn('id', $selectedIds);
                }
            )
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Kirim ke halaman cetaklabel
        |--------------------------------------------------------------------------
        */

        return view(
            'cetaklabel',
            compact('products')
        );
    }
}
