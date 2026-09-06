@extends('layouts.app')

@section('header', 'Manajemen Stok & Layanan Konter')

@section('content')

<div class="space-y-6 pb-12">{{-- =========================================================
     NOTIFIKASI
========================================================== --}}

@if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/30
                text-emerald-400 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-rose-500/10 border border-rose-500/30
                text-rose-400 px-4 py-3 rounded-xl text-sm">
        {{ session('error') }}
    </div>
@endif


{{-- =========================================================
     HEADER AKSI
========================================================== --}}

<div class="flex flex-col sm:flex-row
            justify-between items-start sm:items-center
            gap-4 bg-gray-800/80 border border-white/10
            rounded-2xl p-4 shadow-xl backdrop-blur-md">

    <div>
        <h3 class="text-base font-semibold text-white">
            Daftar Inventaris Konter HP
        </h3>

        <p class="text-xs text-gray-400 mt-1">
            Atur harga jual, stok fisik, dan jenis layanan servis di sini.
        </p>
    </div>

    <div class="flex flex-wrap gap-2">

        {{-- CETAK LABEL HARGA --}}
        <a
            href="{{ route('cetaklabel') }}"
            class="bg-emerald-600 hover:bg-emerald-500
                   text-white text-xs font-medium
                   px-4 py-2.5 rounded-xl transition shadow
                   inline-flex items-center gap-2"
        >
            🏷️ Cetak Label
        </a>

        {{-- TAMBAH PRODUK --}}
        <button
            onclick="toggleProductModal()"
            class="bg-indigo-600 hover:bg-indigo-500
                   text-white text-xs font-medium
                   px-4 py-2.5 rounded-xl transition shadow"
        >
            + Tambah Produk / Servis
        </button>

    </div>

</div>


{{-- =========================================================
     PENCARIAN & FILTER
========================================================== --}}

<div class="bg-gray-800/80 border border-white/10
            rounded-2xl p-4 shadow-xl backdrop-blur-md">

    <form
        action="{{ route('produk.index') }}"
        method="GET"
        id="filterForm"
        class="flex flex-col sm:flex-row gap-2"
    >

        {{-- Search --}}
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari nama, kategori, atau SKU..."
            class="w-full bg-gray-900 border border-white/10
                   rounded-xl px-4 py-2.5 text-white text-xs
                   outline-none focus:ring-2 focus:ring-indigo-500"
        >

        {{-- Category --}}
        <select
            name="category"
            onchange="document.getElementById('filterForm').submit()"
            class="bg-gray-900 border border-white/10
                   rounded-xl px-4 py-2.5 text-white text-xs
                   outline-none focus:ring-2 focus:ring-indigo-500
                   cursor-pointer sm:w-56"
        >

            <option
                value="all"
                {{ (!$category || $category == 'all') ? 'selected' : '' }}
            >
                Semua Kategori
            </option>

            @isset($categories)

                @foreach($categories as $cat)

                    <option
                        value="{{ $cat }}"
                        {{ ($category == $cat) ? 'selected' : '' }}
                    >
                        {{ $cat }}
                    </option>

                @endforeach

            @endisset

        </select>


        {{-- Search Button --}}
        <button
            type="submit"
            class="bg-indigo-600 hover:bg-indigo-500
                   text-white px-5 py-2.5
                   rounded-xl text-xs font-medium transition"
        >
            Cari
        </button>


        {{-- Reset --}}
        @if(
            request('search') ||
            (request('category') && request('category') !== 'all')
        )

            <a
                href="{{ route('produk.index') }}"
                class="bg-gray-700 hover:bg-gray-600
                       text-gray-300 px-3 py-2.5
                       rounded-xl text-xs font-medium
                       flex items-center justify-center transition"
            >
                Reset
            </a>

        @endif

    </form>

</div>


{{-- =========================================================
     TABEL PRODUK
========================================================== --}}

<div class="bg-gray-800/80 border border-white/10
            rounded-2xl p-6 shadow-xl backdrop-blur-md">

    <div class="flex items-center justify-between mb-4">

        <div>
            <h3 class="text-base font-semibold text-white">
                Stok Produk
            </h3>

            <p class="text-xs text-gray-400 mt-1">
                Indikator menunjukkan kondisi stok saat ini.
            </p>
        </div>

        {{-- Legenda Stok --}}
        <div class="hidden md:flex items-center gap-3 text-[10px]">

            <span class="flex items-center gap-1 text-emerald-400">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Aman
            </span>

            <span class="flex items-center gap-1 text-yellow-400">
                <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                Rendah
            </span>

            <span class="flex items-center gap-1 text-orange-400">
                <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                Menipis
            </span>

            <span class="flex items-center gap-1 text-rose-400">
                <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                Habis
            </span>

        </div>

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse min-w-[800px]">

            <thead>

                <tr class="border-b border-white/10
                           text-xs text-gray-400
                           uppercase tracking-wider">

                    <th class="py-3 px-4">
                        Kode / SKU
                    </th>

                    <th class="py-3 px-4">
                        Nama & Merek
                    </th>

                    <th class="py-3 px-4">
                        Kategori
                    </th>

                    <th class="py-3 px-4">
                        Harga Modal
                    </th>

                    <th class="py-3 px-4">
                        Harga Jual
                    </th>

                    <th class="py-3 px-4">
                        Ketersediaan
                    </th>

                    @if(session('user_role') === 'admin')

                        <th class="py-3 px-4 text-right">
                            Aksi
                        </th>

                    @endif

                </tr>

            </thead>


            <tbody class="divide-y divide-white/5 text-sm">

                @forelse($products as $p)

                    <tr class="hover:bg-white/5 transition">


                        {{-- SKU --}}
                        <td class="py-3.5 px-4">

                            <span class="font-mono text-xs text-indigo-400">
                                {{ $p->sku }}
                            </span>

                        </td>


                        {{-- NAMA --}}
                        <td class="py-3.5 px-4">

                            <div class="flex items-center gap-3">

@if($p->image)
    <img
        src="{{ asset('products/' . $p->image) }}"
        alt="{{ $p->name }}"
        class="w-10 h-10 rounded-lg object-cover border border-white/10 shrink-0"
    >
@else
    <div class="w-10 h-10 rounded-lg bg-gray-700/50 flex items-center justify-center text-gray-400 text-[10px] border border-white/10 shrink-0">
        No Img
    </div>
@endif


                                <div>

                                    <div class="font-medium text-white">
                                        {{ $p->name }}
                                    </div>

                                    @if($p->brand)

                                        <span class="text-xs text-gray-400">
                                            Brand: {{ $p->brand }}
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </td>


                        {{-- KATEGORI --}}
                        <td class="py-3.5 px-4">

                            <span
                                class="px-2.5 py-1 rounded-full
                                       text-xs font-semibold
                                       bg-gray-700/50 text-gray-300
                                       border border-white/10"
                            >
                                {{ $p->category }}
                            </span>

                        </td>


                        {{-- MODAL --}}
                        <td class="py-3.5 px-4 text-gray-400 text-xs">

                            Rp {{ number_format(
                                $p->capital_price,
                                0,
                                ',',
                                '.'
                            ) }}

                        </td>


                        {{-- HARGA JUAL --}}
                        <td class="py-3.5 px-4
                                   text-emerald-400 font-medium">

                            Rp {{ number_format(
                                $p->price,
                                0,
                                ',',
                                '.'
                            ) }}

                        </td>


                        {{-- =================================================
                             INDIKATOR STOK
                        ================================================== --}}

                        <td class="py-3.5 px-4">

                            @if($p->stock <= 0)

                                {{-- HABIS --}}
                                <div class="flex items-center gap-2">

                                    <span
                                        class="w-2.5 h-2.5 rounded-full
                                               bg-rose-400
                                               shadow-[0_0_8px_rgba(251,113,133,0.7)]"
                                    ></span>

                                    <div>

                                        <div class="text-rose-400
                                                    text-xs font-bold">
                                            HABIS
                                        </div>

                                        <div class="text-[10px] text-gray-500">
                                            Stok 0
                                        </div>

                                    </div>

                                </div>


                            @elseif($p->stock <= 5)

                                {{-- STOK MENIPIS --}}
                                <div class="flex items-center gap-2">

                                    <span
                                        class="w-2.5 h-2.5 rounded-full
                                               bg-orange-400
                                               shadow-[0_0_8px_rgba(251,146,60,0.7)]"
                                    ></span>

                                    <div>

                                        <div class="text-orange-400
                                                    text-xs font-bold">
                                            STOK MENIPIS
                                        </div>

                                        <div class="text-[10px] text-gray-400">
                                            {{ $p->stock }} Pcs tersisa
                                        </div>

                                    </div>

                                </div>


                            @elseif($p->stock <= 10)

                                {{-- STOK RENDAH --}}
                                <div class="flex items-center gap-2">

                                    <span
                                        class="w-2.5 h-2.5 rounded-full
                                               bg-yellow-400
                                               shadow-[0_0_8px_rgba(250,204,21,0.7)]"
                                    ></span>

                                    <div>

                                        <div class="text-yellow-400
                                                    text-xs font-bold">
                                            STOK RENDAH
                                        </div>

                                        <div class="text-[10px] text-gray-400">
                                            {{ $p->stock }} Pcs
                                        </div>

                                    </div>

                                </div>


                            @else

                                {{-- STOK AMAN --}}
                                <div class="flex items-center gap-2">

                                    <span
                                        class="w-2.5 h-2.5 rounded-full
                                               bg-emerald-400
                                               shadow-[0_0_8px_rgba(52,211,153,0.7)]"
                                    ></span>

                                    <div>

                                        <div class="text-emerald-400
                                                    text-xs font-semibold">
                                            STOK AMAN
                                        </div>

                                        <div class="text-[10px] text-gray-400">
                                            {{ $p->stock }} Pcs
                                        </div>

                                    </div>

                                </div>

                            @endif

                        </td>


                        {{-- AKSI --}}
                        @if(session('user_role') === 'admin')

                            <td class="py-3.5 px-4">

                                <div class="flex items-center
                                            justify-end gap-1.5">

                                    {{-- EDIT --}}
                                    <button
                                        onclick="openEditModal(
                                            {{ $p->id }},
                                            {{ json_encode($p->name) }},
                                            {{ json_encode($p->category) }},
                                            {{ json_encode($p->brand) }},
                                            {{ $p->capital_price }},
                                            {{ $p->price }},
                                            {{ $p->stock }}
                                        )"
                                        class="bg-amber-600/20
                                               hover:bg-amber-600
                                               text-amber-400
                                               hover:text-white
                                               px-2.5 py-1.5
                                               rounded-lg text-xs
                                               font-medium transition
                                               border border-amber-500/30"
                                    >
                                        Edit
                                    </button>


                                    {{-- HAPUS --}}
                                    <form
                                        action="{{ route('produk.destroy', $p->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus produk ini?')"
                                        class="inline"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="bg-rose-600/20
                                                   hover:bg-rose-600
                                                   text-rose-400
                                                   hover:text-white
                                                   px-2.5 py-1.5
                                                   rounded-lg text-xs
                                                   font-medium transition
                                                   border border-rose-500/30"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        @endif

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="{{ session('user_role') === 'admin' ? 7 : 6 }}"
                            class="text-center py-8
                                   text-gray-400 text-sm"
                        >
                            Belum ada produk atau data tidak ditemukan
                            pada pencarian ini.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}
    @if($products->hasPages())

        <div
            class="pt-4 mt-4 border-t border-white/10
                   flex justify-center"
        >
            {{ $products->links() }}
        </div>

    @endif

</div>


{{-- =========================================================
     RIWAYAT PENAMBAHAN PRODUK
========================================================== --}}

<div
    class="bg-gray-800/80 border border-white/10
           rounded-2xl p-6 shadow-xl
           backdrop-blur-md mt-6"
>

    <div
        class="flex flex-col sm:flex-row
               justify-between items-start sm:items-center
               gap-4 mb-6 pb-4 border-b border-white/10"
    >

        <div>

            <h3 class="text-base font-semibold text-white">
                Riwayat Penambahan Produk Harian
            </h3>

            <p class="text-xs text-gray-400 mt-1">
                Rekap produk baru dan restock berdasarkan tanggal.
            </p>

        </div>


        {{-- FILTER RIWAYAT --}}
        <form
            action="{{ route('produk.index') }}"
            method="GET"
            id="dateFilterForm"
            class="flex flex-wrap items-center gap-2"
        >

            <input
                type="hidden"
                name="search"
                value="{{ request('search') }}"
            >

            <input
                type="hidden"
                name="category"
                value="{{ request('category') }}"
            >


            <select
                name="date_filter"
                id="dateFilterSelect"
                onchange="handleDateFilterChange(this.value)"
                class="bg-gray-900 border border-white/10
                       rounded-xl px-3 py-2 text-white
                       text-xs outline-none
                       focus:ring-2 focus:ring-indigo-500"
            >

                <option value="all"
                    {{ ($dateFilter ?? 'all') == 'all' ? 'selected' : '' }}>
                    Semua Waktu
                </option>

                <option value="today"
                    {{ ($dateFilter ?? '') == 'today' ? 'selected' : '' }}>
                    Hari Ini
                </option>

                <option value="7days"
                    {{ ($dateFilter ?? '') == '7days' ? 'selected' : '' }}>
                    7 Hari Terakhir
                </option>

                <option value="30days"
                    {{ ($dateFilter ?? '') == '30days' ? 'selected' : '' }}>
                    30 Hari Terakhir
                </option>

                <option value="3months"
                    {{ ($dateFilter ?? '') == '3months' ? 'selected' : '' }}>
                    3 Bulan Terakhir
                </option>

                <option value="1year"
                    {{ ($dateFilter ?? '') == '1year' ? 'selected' : '' }}>
                    1 Tahun Terakhir
                </option>

                <option value="3years"
                    {{ ($dateFilter ?? '') == '3years' ? 'selected' : '' }}>
                    3 Tahun Terakhir
                </option>

                <option value="5years"
                    {{ ($dateFilter ?? '') == '5years' ? 'selected' : '' }}>
                    5 Tahun Terakhir
                </option>

                <option value="custom"
                    {{ ($dateFilter ?? '') == 'custom' ? 'selected' : '' }}>
                    Rentang Tanggal
                </option>

            </select>


            {{-- CUSTOM DATE --}}
            <div
                id="customDateInputs"
                class="hidden flex items-center gap-2"
            >

                <input
                    type="date"
                    name="start_date"
                    value="{{ $startDate ?? '' }}"
                    class="bg-gray-900 border border-white/10
                           rounded-xl px-2 py-1.5
                           text-white text-xs outline-none"
                >

                <span class="text-gray-400 text-xs">
                    s/d
                </span>

                <input
                    type="date"
                    name="end_date"
                    value="{{ $endDate ?? '' }}"
                    class="bg-gray-900 border border-white/10
                           rounded-xl px-2 py-1.5
                           text-white text-xs outline-none"
                >

                <button
                    type="submit"
                    class="bg-indigo-600 hover:bg-indigo-500
                           text-white px-3 py-1.5
                           rounded-xl text-xs font-medium transition"
                >
                    Terapkan
                </button>

            </div>

        </form>

    </div>


    {{-- TABEL RIWAYAT --}}
    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">

            <thead>

                <tr
                    class="border-b border-white/10
                           text-xs text-gray-400
                           uppercase tracking-wider"
                >

                    <th class="py-3 px-3 w-28">
                        Tanggal
                    </th>

                    <th class="py-3 px-3 w-32">
                        Aktivitas
                    </th>

                    <th class="py-3 px-0">
                        Produk
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-white/10 text-sm">

                @forelse($historyGroups as $date => $items)

                    <tr class="hover:bg-white/5 transition align-top">

                        {{-- TANGGAL --}}
                        <td
                            class="py-3 px-3
                                   text-indigo-400
                                   font-medium text-xs
                                   whitespace-nowrap"
                        >
                            {{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}
                        </td>


                        {{-- JUMLAH --}}
                        <td
                            class="py-3 px-3
                                   text-emerald-400
                                   font-semibold text-xs
                                   whitespace-nowrap"
                        >
                            {{ $items->count() }} Aktivitas
                        </td>


                        {{-- PRODUK --}}
                        <td class="py-0 px-0">

                            <table
                                class="w-full text-left
                                       border-collapse
                                       divide-y divide-white/5"
                            >

                                @foreach($items as $hist)

                                    <tr class="hover:bg-white/5 transition">

                                        {{-- SKU --}}
                                        <td
                                            class="py-2.5 px-3
                                                   text-indigo-300
                                                   font-mono text-xs w-28"
                                        >
                                            {{ $hist->sku }}
                                        </td>


                                        {{-- PRODUK --}}
                                        <td
                                            class="py-2.5 px-3
                                                   text-white text-xs
                                                   font-medium"
                                        >

                                            <div class="flex
                                                        items-center
                                                        gap-2
                                                        flex-wrap">

                                                <span>
                                                    {{ $hist->name }}
                                                </span>

                                                @if(
                                                    str_contains(
                                                        $hist->status_type,
                                                        'Produk Baru'
                                                    )
                                                )

                                                    <span
                                                        class="bg-indigo-500/20
                                                               text-indigo-300
                                                               border
                                                               border-indigo-500/30
                                                               text-[10px]
                                                               px-2 py-0.5
                                                               rounded-full
                                                               font-semibold"
                                                    >
                                                        Baru
                                                    </span>

                                                @elseif(
                                                    str_contains(
                                                        $hist->status_type,
                                                        'Restock'
                                                    )
                                                )

                                                    <span
                                                        class="bg-emerald-500/20
                                                               text-emerald-300
                                                               border
                                                               border-emerald-500/30
                                                               text-[10px]
                                                               px-2 py-0.5
                                                               rounded-full
                                                               font-semibold"
                                                    >
                                                        Restock
                                                    </span>

                                                @else

                                                    <span
                                                        class="bg-gray-500/20
                                                               text-gray-300
                                                               border
                                                               border-gray-500/30
                                                               text-[10px]
                                                               px-2 py-0.5
                                                               rounded-full
                                                               font-semibold"
                                                    >
                                                        {{ str_contains($hist->status_type, 'Edit Produk') ? 'Edit' : 'Aktivitas' }}
                                                    </span>

                                                @endif

                                            </div>

                                        </td>


                                        {{-- JUMLAH --}}
                                        <td
                                            class="py-2.5 px-3
                                                   text-emerald-400
                                                   font-semibold text-xs
                                                   w-40 text-right
                                                   align-middle"
                                        >

                                            <div
                                                class="flex items-center
                                                       justify-end gap-2"
                                            >

                                                <span>
                                                    @if($hist->added_stock > 0)
                                                        +{{ $hist->added_stock }} pcs
                                                    @else
                                                        -
                                                    @endif
                                                </span>


                                                {{-- HAPUS RIWAYAT --}}
                                                @if(session('user_role') === 'admin')

                                                    <form
                                                        action="{{ route('produk.history.destroy', $hist->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Hapus riwayat ini dan kurangi stok utama?')"
                                                        class="inline"
                                                    >

                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="text-rose-400
                                                                   hover:text-rose-300
                                                                   bg-rose-500/10
                                                                   hover:bg-rose-500/20
                                                                   p-1 rounded
                                                                   transition"
                                                            title="Hapus / Koreksi Riwayat"
                                                        >
                                                            ✕
                                                        </button>

                                                    </form>

                                                @endif

                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            </table>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="3"
                            class="text-center py-6
                                   text-gray-400 text-xs"
                        >
                            Tidak ada riwayat penambahan produk
                            pada rentang waktu ini.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

</div>{{-- =========================================================
MODAL TAMBAH PRODUK
========================================================== --}}

<div
    id="product-modal"
    class="hidden fixed inset-0 z-50
           flex items-center justify-center
           p-4 bg-black/60 backdrop-blur-sm"
><div
    class="bg-gray-800 border border-white/15
           rounded-2xl w-full max-w-lg p-6
           shadow-2xl relative"
>

    <div
        class="flex justify-between items-center
               pb-3 border-b border-white/10 mb-4"
    >

        <h3 class="text-lg font-semibold text-white">
            Tambah Produk / Servis Baru
        </h3>

        <button
            onclick="toggleProductModal()"
            class="text-gray-400 hover:text-white
                   text-sm font-bold px-2 py-1
                   rounded-lg bg-gray-700/50"
        >
            ✕
        </button>

    </div>


    <form
        action="{{ route('produk.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-4"
    >

        @csrf


        {{-- KATEGORI --}}
        <div>

            <label class="block text-xs font-medium
                          text-gray-300 mb-1">
                Kategori Produk
            </label>

            <input
                type="text"
                name="category"
                placeholder="Contoh: Aksesoris / Sparepart"
                class="w-full bg-gray-900
                       border border-white/10
                       rounded-xl px-4 py-2.5
                       text-white text-sm
                       focus:ring-2 focus:ring-indigo-500
                       outline-none"
                required
            >

        </div>


        {{-- BRAND --}}
        <div>

            <label class="block text-xs font-medium
                          text-gray-300 mb-1">
                Merek / Brand (Opsional)
            </label>

            <input
                type="text"
                name="brand"
                placeholder="Contoh: Vivan / Robot / Ori"
                class="w-full bg-gray-900
                       border border-white/10
                       rounded-xl px-4 py-2.5
                       text-white text-sm
                       focus:ring-2 focus:ring-indigo-500
                       outline-none"
            >

        </div>


        {{-- NAMA --}}
        <div>

            <label class="block text-xs font-medium
                          text-gray-300 mb-1">
                Nama Barang / Layanan
            </label>

            <input
                type="text"
                name="name"
                placeholder="Contoh: Kabel Data Fast Charging"
                class="w-full bg-gray-900
                       border border-white/10
                       rounded-xl px-4 py-2.5
                       text-white text-sm
                       focus:ring-2 focus:ring-indigo-500
                       outline-none"
                required
            >

        </div>


        {{-- HARGA --}}
        <div class="grid grid-cols-2 gap-4">

            <div>

                <label class="block text-xs font-medium
                              text-gray-300 mb-1">
                    Harga Modal (Rp)
                </label>

                <input
                    type="number"
                    name="capital_price"
                    placeholder="20000"
                    min="0"
                    class="w-full bg-gray-900
                           border border-white/10
                           rounded-xl px-4 py-2.5
                           text-white text-sm
                           focus:ring-2 focus:ring-indigo-500
                           outline-none"
                    required
                >

            </div>


            <div>

                <label class="block text-xs font-medium
                              text-gray-300 mb-1">
                    Harga Jual (Rp)
                </label>

                <input
                    type="number"
                    name="price"
                    placeholder="35000"
                    min="0"
                    class="w-full bg-gray-900
                           border border-white/10
                           rounded-xl px-4 py-2.5
                           text-white text-sm
                           focus:ring-2 focus:ring-indigo-500
                           outline-none"
                    required
                >

            </div>

        </div>


        {{-- STOK --}}
        <div>

            <label class="block text-xs font-medium
                          text-gray-300 mb-1">
                Stok Fisik / Jumlah Masuk
            </label>

            <input
                type="number"
                name="stock"
                value="10"
                min="0"
                class="w-full bg-gray-900
                       border border-white/10
                       rounded-xl px-4 py-2.5
                       text-white text-sm
                       focus:ring-2 focus:ring-indigo-500
                       outline-none"
                required
            >

        </div>


        {{-- FOTO --}}
        <div>

            <label class="block text-xs font-medium
                          text-gray-300 mb-1">
                Foto Produk / Servis
            </label>

            <input
                type="file"
                name="image"
                accept="image/*"
                class="w-full bg-gray-900
                       border border-white/10
                       rounded-xl px-4 py-2
                       text-white text-xs
                       file:mr-4 file:py-1
                       file:px-4 file:rounded-lg
                       file:border-0
                       file:text-xs
                       file:font-semibold
                       file:bg-indigo-600
                       file:text-white
                       hover:file:bg-indigo-500
                       outline-none"
            >

        </div>


        {{-- BUTTON --}}
        <div class="flex justify-end
                    space-x-2 pt-2">

            <button
                type="button"
                onclick="toggleProductModal()"
                class="bg-gray-700 hover:bg-gray-600
                       text-gray-300 px-4 py-2.5
                       rounded-xl text-xs
                       font-medium transition"
            >
                Batal
            </button>

            <button
                type="submit"
                class="bg-indigo-600 hover:bg-indigo-500
                       text-white px-5 py-2.5
                       rounded-xl text-xs
                       font-medium transition shadow"
            >
                Simpan Produk
            </button>

        </div>

    </form>

</div>

</div>{{-- =========================================================
MODAL EDIT PRODUK
========================================================== --}}

<div
    id="edit-product-modal"
    class="hidden fixed inset-0 z-50
           flex items-center justify-center
           p-4 bg-black/60 backdrop-blur-sm"
><div
    class="bg-gray-800 border border-white/15
           rounded-2xl w-full max-w-lg p-6
           shadow-2xl relative
           max-h-[90vh] overflow-y-auto"
>

    <div
        class="flex justify-between items-center
               pb-3 border-b border-white/10 mb-4"
    >

        <h3 class="text-lg font-semibold text-white">
            Edit Data Produk / Servis
        </h3>

        <button
            onclick="toggleEditModal()"
            class="text-gray-400 hover:text-white
                   text-sm font-bold px-2 py-1
                   rounded-lg bg-gray-700/50"
        >
            ✕
        </button>

    </div>


    <form
        id="editProductForm"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-4"
    >

        @csrf
        @method('PUT')


        {{-- KATEGORI --}}
        <div>

            <label class="block text-xs font-medium
                          text-gray-300 mb-1">
                Kategori Produk
            </label>

            <input
                type="text"
                id="edit_category"
                name="category"
                class="w-full bg-gray-900
                       border border-white/10
                       rounded-xl px-4 py-2.5
                       text-white text-sm
                       focus:ring-2 focus:ring-indigo-500
                       outline-none"
                required
            >

        </div>


        {{-- BRAND --}}
        <div>

            <label class="block text-xs font-medium
                          text-gray-300 mb-1">
                Merek / Brand (Opsional)
            </label>

            <input
                type="text"
                id="edit_brand"
                name="brand"
                class="w-full bg-gray-900
                       border border-white/10
                       rounded-xl px-4 py-2.5
                       text-white text-sm
                       focus:ring-2 focus:ring-indigo-500
                       outline-none"
            >

        </div>


        {{-- NAMA --}}
        <div>

            <label class="block text-xs font-medium
                          text-gray-300 mb-1">
                Nama Barang / Layanan
            </label>

            <input
                type="text"
                id="edit_name"
                name="name"
                class="w-full bg-gray-900
                       border border-white/10
                       rounded-xl px-4 py-2.5
                       text-white text-sm
                       focus:ring-2 focus:ring-indigo-500
                       outline-none"
                required
            >

        </div>


        {{-- HARGA --}}
        <div class="grid grid-cols-2 gap-4">

            <div>

                <label class="block text-xs font-medium
                              text-gray-300 mb-1">
                    Harga Modal (Rp)
                </label>

                <input
                    type="number"
                    id="edit_capital_price"
                    name="capital_price"
                    min="0"
                    class="w-full bg-gray-900
                           border border-white/10
                           rounded-xl px-4 py-2.5
                           text-white text-sm
                           focus:ring-2 focus:ring-indigo-500
                           outline-none"
                    required
                >

            </div>


            <div>

                <label class="block text-xs font-medium
                              text-gray-300 mb-1">
                    Harga Jual (Rp)
                </label>

                <input
                    type="number"
                    id="edit_price"
                    name="price"
                    min="0"
                    class="w-full bg-gray-900
                           border border-white/10
                           rounded-xl px-4 py-2.5
                           text-white text-sm
                           focus:ring-2 focus:ring-indigo-500
                           outline-none"
                    required
                >

            </div>

        </div>


        {{-- STOK --}}
        <div>

            <label class="block text-xs font-medium
                          text-gray-300 mb-1">
                Stok Fisik
            </label>

            <input
                type="number"
                id="edit_stock"
                name="stock"
                min="0"
                class="w-full bg-gray-900
                       border border-white/10
                       rounded-xl px-4 py-2.5
                       text-white text-sm
                       focus:ring-2 focus:ring-indigo-500
                       outline-none"
                required
            >

        </div>


        {{-- FOTO --}}
        <div>

            <label class="block text-xs font-medium
                          text-gray-300 mb-1">
                Ganti Foto Produk (Opsional)
            </label>

            <input
                type="file"
                name="image"
                accept="image/*"
                class="w-full bg-gray-900
                       border border-white/10
                       rounded-xl px-4 py-2
                       text-white text-xs
                       file:mr-4 file:py-1
                       file:px-4 file:rounded-lg
                       file:border-0
                       file:text-xs
                       file:font-semibold
                       file:bg-amber-600
                       file:text-white
                       hover:file:bg-amber-500
                       outline-none"
            >

        </div>


        {{-- BUTTON --}}
        <div class="flex justify-end
                    space-x-2 pt-2">

            <button
                type="button"
                onclick="toggleEditModal()"
                class="bg-gray-700 hover:bg-gray-600
                       text-gray-300 px-4 py-2.5
                       rounded-xl text-xs
                       font-medium transition"
            >
                Batal
            </button>

            <button
                type="submit"
                class="bg-amber-600 hover:bg-amber-500
                       text-white px-5 py-2.5
                       rounded-xl text-xs
                       font-medium transition shadow"
            >
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>

</div>{{-- =========================================================
JAVASCRIPT
========================================================== --}}

<script>

    /*
    |--------------------------------------------------------------------------
    | MODAL TAMBAH
    |--------------------------------------------------------------------------
    */

    function toggleProductModal() {

        const modal =
            document.getElementById('product-modal');

        modal.classList.toggle('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | MODAL EDIT
    |--------------------------------------------------------------------------
    */

    function toggleEditModal() {

        const modal =
            document.getElementById('edit-product-modal');

        modal.classList.toggle('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | BUKA MODAL EDIT
    |--------------------------------------------------------------------------
    */

    function openEditModal(
        id,
        name,
        category,
        brand,
        capital_price,
        price,
        stock
    ) {

        document.getElementById(
            'editProductForm'
        ).action =
            "{{ url('produk') }}/" + id;


        document.getElementById(
            'edit_name'
        ).value = name;


        document.getElementById(
            'edit_category'
        ).value = category;


        document.getElementById(
            'edit_brand'
        ).value =
            brand === null
                ? ''
                : brand;


        document.getElementById(
            'edit_capital_price'
        ).value = capital_price;


        document.getElementById(
            'edit_price'
        ).value = price;


        document.getElementById(
            'edit_stock'
        ).value = stock;


        toggleEditModal();
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL RIWAYAT
    |--------------------------------------------------------------------------
    */

    function handleDateFilterChange(value) {

        const customInputs =
            document.getElementById(
                'customDateInputs'
            );


        if (value === 'custom') {

            customInputs.classList.remove(
                'hidden'
            );

        } else {

            customInputs.classList.add(
                'hidden'
            );

            document
                .getElementById('dateFilterForm')
                .submit();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SAAT HALAMAN DIBUKA
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'DOMContentLoaded',
        function () {

            const select =
                document.getElementById(
                    'dateFilterSelect'
                );

            const customInputs =
                document.getElementById(
                    'customDateInputs'
                );


            if (
                select &&
                select.value === 'custom'
            ) {

                customInputs.classList.remove(
                    'hidden'
                );
            }

        }
    );

</script>@endsection