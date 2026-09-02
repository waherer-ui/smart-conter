@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

<div class="space-y-6">{{-- =========================================================
     HEADER DASHBOARD
========================================================== --}}
<div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-xl">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <h2 class="text-xl font-bold">
                Etalase & Servis Konter HP
            </h2>

            <p class="text-sm text-blue-100 mt-1">
                Lihat produk, layanan, harga, dan ketersediaan stok.
            </p>
        </div>

        <div class="text-left sm:text-right">
            <p class="text-xs text-blue-100">
                Total Produk
            </p>

            <p class="text-2xl font-bold">
                {{ $products->count() }}
            </p>
        </div>

    </div>

</div>


{{-- =========================================================
     {{-- FILTER KATEGORI DROPDOWN --}}
<div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4 shadow-xl backdrop-blur-md mb-6">
    <form action="{{ route('home') }}" method="GET" id="dashboardCategoryForm">
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 font-medium whitespace-nowrap">
                Filter Kategori:
            </span>
            <select
                name="category"
                onchange="document.getElementById('dashboardCategoryForm').submit()"
                class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2.5 text-white text-xs outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer"
            >
                <option value="all" {{ (!$category || $category == 'all') ? 'selected' : '' }}>
                    Semua Kategori
                </option>

                @isset($categories)
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ ($category == $cat) ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                @endisset
            </select>
        </div>
    </form>
</div>



{{-- =========================================================
     JUDUL PRODUK
========================================================== --}}
<div class="flex items-center justify-between">

    <div>

        <h2 class="text-lg font-semibold text-white">
            Produk Tersedia
        </h2>

        <p class="text-sm text-gray-400 mt-1">
            {{ $products->count() }} produk ditampilkan
        </p>

    </div>

</div>


{{-- =========================================================
     DAFTAR PRODUK
========================================================== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    @forelse($products as $p)

        <div
            class="bg-gray-800/80
                   border border-white/10
                   rounded-2xl
                   p-4
                   shadow-lg
                   flex flex-col
                   justify-between
                   hover:border-indigo-500/40
                   hover:bg-gray-800
                   transition"
        >

            {{-- =================================================
                 INFORMASI PRODUK
            ================================================== --}}
            <div>

                {{-- Kategori & SKU --}}
                <div class="flex items-center justify-between gap-2 mb-3">

                    <span
                        class="px-2.5 py-1
                               rounded-full
                               text-[10px]
                               font-semibold
                               bg-indigo-500/10
                               text-indigo-400
                               border border-indigo-500/20
                               truncate"
                    >
                        {{ $p->category }}
                    </span>

                    <span
                        class="text-[10px]
                               font-mono
                               text-gray-500
                               truncate"
                    >
                        {{ $p->sku }}
                    </span>

                </div>
                
              {{-- THUMBNAIL GAMBAR PRODUK --}}
<div class="w-full h-28 mb-3 bg-gray-800 rounded-xl overflow-hidden flex items-center justify-center border border-white/5">
    @if($p->image)
        <img src="{{ asset('products/' . $p->image) }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
    @else
        <span class="text-[10px] text-gray-500">No Image</span>
    @endif
</div>


                {{-- Nama Produk --}}
                <h3
                    class="text-white
                           font-semibold
                           text-sm
                           leading-5"
                >
                    {{ $p->name }}
                </h3>


                {{-- Brand --}}
                @if($p->brand)

                    <p class="text-xs text-gray-400 mt-1">
                        {{ $p->brand }}
                    </p>

                @endif


                {{-- Harga --}}
                <div class="mt-4">

                    <p class="text-[11px] text-gray-500">
                        Harga Jual
                    </p>

                    <p class="text-lg font-bold text-emerald-400">
                        Rp {{ number_format($p->price, 0, ',', '.') }}
                    </p>

                </div>

            </div>


            {{-- =================================================
                 INFORMASI STOK
            ================================================== --}}
            <div class="mt-4 pt-3 border-t border-white/10">

                <div class="flex items-center justify-between">

                    <span class="text-xs text-gray-400">
                        Stok
                    </span>


                    @if($p->stock <= 0)

                        <span
                            class="px-2.5 py-1
                                   rounded-full
                                   text-[10px]
                                   font-semibold
                                   bg-red-500/10
                                   text-red-400
                                   border border-red-500/20"
                        >
                            Habis
                        </span>

                    @elseif($p->stock <= 5)

                        <span
                            class="px-2.5 py-1
                                   rounded-full
                                   text-[10px]
                                   font-semibold
                                   bg-yellow-500/10
                                   text-yellow-400
                                   border border-yellow-500/20"
                        >
                            {{ $p->stock }} pcs · Menipis
                        </span>

                    @else

                        <span
                            class="px-2.5 py-1
                                   rounded-full
                                   text-[10px]
                                   font-semibold
                                   bg-emerald-500/10
                                   text-emerald-400
                                   border border-emerald-500/20"
                        >
                            {{ $p->stock }} pcs
                        </span>

                    @endif

                </div>

            </div>

        </div>

    @empty

        {{-- =====================================================
             TIDAK ADA PRODUK
        ====================================================== --}}
        <div
            class="col-span-full
                   bg-gray-800/50
                   border border-white/10
                   rounded-2xl
                   py-14
                   px-6
                   text-center"
        >

            <div class="text-4xl mb-3">
                📦
            </div>

            <h3 class="text-white font-semibold">
                Belum ada produk
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Belum ada produk pada kategori yang dipilih.
            </p>

        </div>

    @endforelse

</div>

</div>{{-- =============================================================
STYLE SCROLL FILTER
============================================================== --}}

<style>

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

</style>@endsection