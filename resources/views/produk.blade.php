@extends('layouts.app')

@section('mobile_action', 'tambah')
@section('content')

<div class="mt-1 mb-3">
    <div class="flex flex-wrap items-center gap-2">

        {{-- TAMBAH PRODUK --}}
        <button
            type="button"
            onclick="toggleProductModal()"
            class="inline-flex items-center justify-center gap-1.5
                   bg-indigo-600 hover:bg-indigo-500
                   text-white text-xs font-semibold
                   px-3 py-2
                   rounded-xl
                   transition
                   shadow-sm"
        >
            <span class="text-sm leading-none">＋</span>
            <span>Tambah Produk / Servis</span>
        </button>

        {{-- CETAK LABEL --}}
        <a
            href="{{ route('cetaklabel') }}"
            class="inline-flex items-center justify-center gap-1.5
                   bg-emerald-600/90 hover:bg-emerald-500
                   text-white text-xs font-semibold
                   px-3 py-2
                   rounded-xl
                   transition
                   shadow-sm"
        >
            <span>🏷️</span>
            <span>Cetak Label</span>
        </a>

    </div>
</div>

<div class="space-y-6 pb-12">

    {{-- =========================================================
         NOTIFIKASI
    ========================================================== --}}

    @if(session('success'))

        <div
            class="bg-emerald-500/10
                   border border-emerald-500/30
                   text-emerald-400
                   px-4 py-3
                   rounded-xl
                   text-sm"
        >
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div
            class="bg-rose-500/10
                   border border-rose-500/30
                   text-rose-400
                   px-4 py-3
                   rounded-xl
                   text-sm"
        >
            {{ session('error') }}
        </div>

    @endif


    {{-- =========================================================
         PENCARIAN & FILTER
    ========================================================== --}}

    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               p-3 sm:p-4
               shadow-xl
               backdrop-blur-md"
    >

        <form
            action="{{ route('produk.index') }}"
            method="GET"
            id="filterForm"
            class="flex gap-2 w-full"
        >

            {{-- SEARCH --}}
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari produk..."
                class="flex-1 min-w-0
                       bg-gray-900
                       border border-white/10
                       rounded-xl
                       px-3 sm:px-4
                       py-2.5
                       text-white
                       text-xs
                       outline-none
                       focus:ring-2
                       focus:ring-indigo-500"
            >


            {{-- CATEGORY --}}
            <select
                name="category"
                onchange="document.getElementById('filterForm').submit()"
                class="w-32 sm:w-56
                       shrink-0
                       bg-gray-900
                       border border-white/10
                       rounded-xl
                       px-2 sm:px-4
                       py-2.5
                       text-white
                       text-xs
                       outline-none
                       focus:ring-2
                       focus:ring-indigo-500
                       cursor-pointer"
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


            {{-- CARI --}}
            <button
                type="submit"
                class="shrink-0
                       bg-indigo-600
                       hover:bg-indigo-500
                       text-white
                       px-3 sm:px-5
                       py-2.5
                       rounded-xl
                       text-xs
                       font-medium
                       transition"
            >
                Cari
            </button>


            {{-- RESET --}}
            @if(
                request('search') ||
                (request('category') && request('category') !== 'all')
            )

                <a
                    href="{{ route('produk.index') }}"
                    class="shrink-0
                           bg-gray-700
                           hover:bg-gray-600
                           text-gray-300
                           px-3
                           py-2.5
                           rounded-xl
                           text-xs
                           font-medium
                           flex
                           items-center
                           justify-center
                           transition"
                >
                    Reset
                </a>

            @endif

        </form>

    </div>


    {{-- =========================================================
         DAFTAR PRODUK
    ========================================================== --}}

    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               p-4 sm:p-6
               shadow-xl
               backdrop-blur-md"
    >

        <div
            class="flex
                   flex-col
                   sm:flex-row
                   sm:items-center
                   sm:justify-between
                   gap-3
                   mb-4"
        >

            <div>

                <h3 class="text-base font-semibold text-white">
                    Daftar Inventaris / Manajemen Stok
                </h3>

                <p class="text-xs text-gray-400 mt-1">
                    Indikator menunjukkan kondisi stok saat ini.
                    Atur harga jual, stok fisik, dan jenis layanan di sini.
                </p>

            </div>


            {{-- LEGENDA STOK --}}
            <div
                class="hidden md:flex
                       items-center
                       gap-3
                       text-[10px]"
            >

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


        {{-- =====================================================
             TABEL PRODUK
        ====================================================== --}}

        <div class="overflow-x-auto">

            <table
                class="w-full
                       text-left
                       border-collapse
                       min-w-[800px]"
            >

                <thead>

                    <tr
                        class="border-b border-white/10
                               text-xs text-gray-400
                               uppercase tracking-wider"
                    >

<th class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
    Kode / SKU
</th>

<th class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
    Nama & Merek
</th>

<th class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
    Kategori
</th>

<th class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
    Harga Modal
</th>

<th class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
    Harga Jual
</th>

<th class="py-2.5 px-3 sm:px-4 whitespace-nowrap">
    Ketersediaan
</th>

                        @if(
                            session('user_role') === 'admin' ||
                            !session('logged_in')
                        )

<th class="py-2.5 px-3 sm:px-4 text-right whitespace-nowrap">
    Aksi
</th>

                        @endif

                    </tr>

                </thead>


                <tbody
                    class="divide-y
                           divide-white/5
                           text-sm"
                >

                    @forelse($products as $p)

                      <tr class="hover:bg-white/[0.035] transition-colors duration-150">

                            {{-- SKU --}}
                            <td class="py-3 px-3 sm:px-4">

                                <span
                                    class="font-mono
                                           text-xs
                                           text-indigo-400"
                                >
                                    {{ $p->sku }}
                                </span>

                            </td>


                            {{-- NAMA --}}
                            <td class="py-3 px-3 sm:px-4">

                                <div class="flex items-center gap-3">

                                    @if($p->image)

                                        <a
                                            href="{{ Storage::disk('s3')->url($p->image) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="block shrink-0"
                                            title="Lihat gambar produk"
                                        >

                                            <img
                                                src="{{ Storage::disk('s3')->url($p->image) }}"
                                                alt="{{ $p->name }}"
                                                class="w-10 h-10
                                                       rounded-lg
                                                       object-cover
                                                       border
                                                       border-white/10"
                                            >

                                        </a>

                                    @else

                                        <div
                                            class="w-10 h-10
                                                   rounded-lg
                                                   bg-gray-700/50
                                                   flex
                                                   items-center
                                                   justify-center
                                                   text-gray-400
                                                   text-[10px]
                                                   border
                                                   border-white/10
                                                   shrink-0"
                                        >
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
                            <td class="py-3 px-3 sm:px-4">

                                <span
                                    class="px-2.5 py-1
                                           rounded-full
                                           text-xs font-semibold
                                           bg-gray-700/50
                                           text-gray-300
                                           border border-white/10"
                                >
                                    {{ $p->category }}
                                </span>

                            </td>


                            {{-- HARGA MODAL --}}
                            <td
                              class="py-3 px-3 sm:px-4 text-gray-400 text-xs">

                                Rp {{ number_format(
                                    $p->capital_price,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>


                            {{-- HARGA JUAL --}}
                            <td class="py-3 px-3 sm:px-4 text-emerald-400 font-medium">

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

                            <td class="py-3 px-3 sm:px-4">

                                @if($p->stock <= 0)

                                    {{-- HABIS --}}
                                    <div class="flex items-center gap-2">

                                        <span
                                            class="w-2.5 h-2.5
                                                   rounded-full
                                                   bg-rose-400
                                                   shadow-[0_0_8px_rgba(251,113,133,0.7)]"
                                        ></span>

                                        <div>

                                            <div
                                                class="text-rose-400
                                                       text-xs
                                                       font-bold"
                                            >
                                                HABIS
                                            </div>

                                            <div class="text-[10px] text-gray-500">
                                                Stok 0
                                            </div>

                                        </div>

                                    </div>


                                @elseif($p->stock <= 5)

                                    {{-- MENIPIS --}}
                                    <div class="flex items-center gap-2">

                                        <span
                                            class="w-2.5 h-2.5
                                                   rounded-full
                                                   bg-orange-400
                                                   shadow-[0_0_8px_rgba(251,146,60,0.7)]"
                                        ></span>

                                        <div>

                                            <div
                                                class="text-orange-400
                                                       text-xs
                                                       font-bold"
                                            >
                                                STOK MENIPIS
                                            </div>

                                            <div class="text-[10px] text-gray-400">
                                                {{ $p->stock }} Pcs tersisa
                                            </div>

                                        </div>

                                    </div>


                                @elseif($p->stock <= 10)

                                    {{-- RENDAH --}}
                                    <div class="flex items-center gap-2">

                                        <span
                                            class="w-2.5 h-2.5
                                                   rounded-full
                                                   bg-yellow-400
                                                   shadow-[0_0_8px_rgba(250,204,21,0.7)]"
                                        ></span>

                                        <div>

                                            <div
                                                class="text-yellow-400
                                                       text-xs
                                                       font-bold"
                                            >
                                                STOK RENDAH
                                            </div>

                                            <div class="text-[10px] text-gray-400">
                                                {{ $p->stock }} Pcs
                                            </div>

                                        </div>

                                    </div>


                                @else

                                    {{-- AMAN --}}
                                    <div class="flex items-center gap-2">

                                        <span
                                            class="w-2.5 h-2.5
                                                   rounded-full
                                                   bg-emerald-400
                                                   shadow-[0_0_8px_rgba(52,211,153,0.7)]"
                                        ></span>

                                        <div>

                                            <div
                                                class="text-emerald-400
                                                       text-xs
                                                       font-semibold"
                                            >
                                                STOK AMAN
                                            </div>

                                            <div class="text-[10px] text-gray-400">
                                                {{ $p->stock }} Pcs
                                            </div>

                                        </div>

                                    </div>

                                @endif

                            </td>


                            {{-- =================================================
                                 AKSI
                            ================================================== --}}

                            @if(
                                session('user_role') === 'admin' ||
                                !session('logged_in')
                            )

                                <td class="py-3 px-3 sm:px-4">

                                    <div
                                        class="flex
                                               items-center
                                               justify-end
                                               gap-1.5"
                                    >

                                        {{-- EDIT --}}
                                        <button
                                            type="button"
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
                                                   rounded-lg
                                                   text-xs
                                                   font-medium
                                                   transition
                                                   border
                                                   border-amber-500/30"
                                        >
                                            Edit
                                        </button>


                                        {{-- HAPUS --}}
                                        @if(session('user_role') === 'admin')

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
                                                           rounded-lg
                                                           text-xs
                                                           font-medium
                                                           transition
                                                           border
                                                           border-rose-500/30"
                                                >
                                                    Hapus
                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            @endif

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="{{
                                    (
                                        session('user_role') === 'admin' ||
                                        !session('logged_in')
                                    )
                                    ? 7
                                    : 6
                                }}"
                                class="text-center
                                       py-8
                                       text-gray-400
                                       text-sm"
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
                class="pt-4 mt-4
                       border-t border-white/10
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
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               p-4 sm:p-6
               shadow-xl
               backdrop-blur-md
               mt-6"
    >

        <div
            class="flex
                   flex-col
                   sm:flex-row
                   justify-between
                   items-start
                   sm:items-center
                   gap-4
                   mb-6
                   pb-4
                   border-b border-white/10"
        >

            <div>

                <h3 class="text-base font-semibold text-white">
                    Riwayat Penambahan Produk Harian
                </h3>

                <p class="text-xs text-gray-400 mt-1">
                    Rekap perubahan stok, produk baru, restock,
                    dan transfer antar toko berdasarkan tanggal.
                </p>

            </div>


            {{-- FILTER RIWAYAT --}}
            <form
                action="{{ route('produk.index') }}"
                method="GET"
                id="dateFilterForm"
                class="flex
                       flex-wrap
                       items-center
                       gap-2"
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
                    class="bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-3 py-2
                           text-white
                           text-xs
                           outline-none
                           focus:ring-2
                           focus:ring-indigo-500"
                >

                    <option
                        value="all"
                        {{ ($dateFilter ?? 'all') == 'all' ? 'selected' : '' }}
                    >
                        Semua Waktu
                    </option>

                    <option
                        value="today"
                        {{ ($dateFilter ?? '') == 'today' ? 'selected' : '' }}
                    >
                        Hari Ini
                    </option>

                    <option
                        value="7days"
                        {{ ($dateFilter ?? '') == '7days' ? 'selected' : '' }}
                    >
                        7 Hari Terakhir
                    </option>

                    <option
                        value="30days"
                        {{ ($dateFilter ?? '') == '30days' ? 'selected' : '' }}
                    >
                        30 Hari Terakhir
                    </option>

                    <option
                        value="3months"
                        {{ ($dateFilter ?? '') == '3months' ? 'selected' : '' }}
                    >
                        3 Bulan Terakhir
                    </option>

                    <option
                        value="1year"
                        {{ ($dateFilter ?? '') == '1year' ? 'selected' : '' }}
                    >
                        1 Tahun Terakhir
                    </option>

                    <option
                        value="3years"
                        {{ ($dateFilter ?? '') == '3years' ? 'selected' : '' }}
                    >
                        3 Tahun Terakhir
                    </option>

                    <option
                        value="5years"
                        {{ ($dateFilter ?? '') == '5years' ? 'selected' : '' }}
                    >
                        5 Tahun Terakhir
                    </option>

                    <option
                        value="custom"
                        {{ ($dateFilter ?? '') == 'custom' ? 'selected' : '' }}
                    >
                        Rentang Tanggal
                    </option>

                </select>


                {{-- CUSTOM DATE --}}
                <div
                    id="customDateInputs"
                    class="hidden
                           flex
                           items-center
                           gap-2
                           flex-wrap"
                >

                    <input
                        type="date"
                        name="start_date"
                        value="{{ $startDate ?? '' }}"
                        class="bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-2 py-1.5
                               text-white
                               text-xs
                               outline-none"
                    >

                    <span class="text-gray-400 text-xs">
                        s/d
                    </span>

                    <input
                        type="date"
                        name="end_date"
                        value="{{ $endDate ?? '' }}"
                        class="bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-2 py-1.5
                               text-white
                               text-xs
                               outline-none"
                    >

                    <button
                        type="submit"
                        class="bg-indigo-600
                               hover:bg-indigo-500
                               text-white
                               px-3 py-1.5
                               rounded-xl
                               text-xs
                               font-medium
                               transition"
                    >
                        Cari
                    </button>

                </div>

            </form>

        </div>


        {{-- =====================================================
             TABEL RIWAYAT
        ====================================================== --}}

        <div class="overflow-x-auto">

            <table
                class="w-full
                       text-left
                       border-collapse"
            >

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


                <tbody
                    class="divide-y
                           divide-white/10
                           text-sm"
                >

                    @forelse($historyGroups as $date => $items)

                        <tr
                            class="hover:bg-white/5
                                   transition
                                   align-top"
                        >

                            {{-- TANGGAL --}}
                            <td
                                class="py-3 px-3
                                       text-indigo-400
                                       font-medium
                                       text-xs
                                       whitespace-nowrap"
                            >
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}
                            </td>


                            {{-- JUMLAH AKTIVITAS --}}
                            <td
                                class="py-3 px-3
                                       text-emerald-400
                                       font-semibold
                                       text-xs
                                       whitespace-nowrap"
                            >
                                {{ $items->count() }} Aktivitas
                            </td>


                            {{-- PRODUK --}}
                            <td class="py-0 px-0">

                                <table
                                    class="w-full
                                           text-left
                                           border-collapse
                                           divide-y
                                           divide-white/5"
                                >

                                    @foreach($items as $hist)

                                        <tr
                                            class="hover:bg-white/5
                                                   transition"
                                        >

                                            {{-- SKU --}}
                                            <td
                                                class="py-2.5 px-3
                                                       text-indigo-300
                                                       font-mono
                                                       text-xs
                                                       w-28"
                                            >
                                                {{ $hist->sku }}
                                            </td>


                                            {{-- PRODUK --}}
                                            <td
                                                class="py-2.5 px-3
                                                       text-white
                                                       text-xs
                                                       font-medium"
                                            >

                                                <div
                                                    class="flex
                                                           items-center
                                                           gap-2
                                                           flex-wrap"
                                                >

                                                    <span>
                                                        {{ $hist->name }}
                                                    </span>


                                                    {{-- PRODUK BARU --}}
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


                                                    {{-- RESTOCK --}}
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


                                                    {{-- TRANSFER MASUK --}}
                                                    @elseif(
                                                        str_contains(
                                                            $hist->status_type,
                                                            'Transfer Masuk'
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
                                                            Transfer Masuk
                                                        </span>


                                                    {{-- TRANSFER KELUAR --}}
                                                    @elseif(
                                                        str_contains(
                                                            $hist->status_type,
                                                            'Transfer Keluar'
                                                        )
                                                    )

                                                        <span
                                                            class="bg-orange-500/20
                                                                   text-orange-300
                                                                   border
                                                                   border-orange-500/30
                                                                   text-[10px]
                                                                   px-2 py-0.5
                                                                   rounded-full
                                                                   font-semibold"
                                                        >
                                                            Transfer Keluar
                                                        </span>


                                                    {{-- LAINNYA --}}
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
                                                            {{
                                                                str_contains(
                                                                    $hist->status_type,
                                                                    'Edit Produk'
                                                                )
                                                                ? 'Edit'
                                                                : 'Aktivitas'
                                                            }}
                                                        </span>

                                                    @endif

                                                </div>

                                            </td>


                                            {{-- JUMLAH --}}
                                            <td
                                                class="py-2.5 px-3
                                                       font-semibold
                                                       text-xs
                                                       w-40
                                                       text-right
                                                       align-middle
                                                       {{
                                                            $hist->added_stock > 0
                                                                ? 'text-emerald-400'
                                                                : (
                                                                    $hist->added_stock < 0
                                                                        ? 'text-orange-400'
                                                                        : 'text-gray-400'
                                                                )
                                                       }}"
                                            >

                                                <div
                                                    class="flex
                                                           items-center
                                                           justify-end
                                                           gap-2"
                                                >

                                                    <span>

                                                        @if($hist->added_stock > 0)

                                                            +{{ $hist->added_stock }} pcs

                                                        @elseif($hist->added_stock < 0)

                                                            {{ $hist->added_stock }} pcs

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
                                                                       p-1
                                                                       rounded
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
                                class="text-center
                                       py-6
                                       text-gray-400
                                       text-xs"
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

</div>


{{-- =========================================================
     MODAL TAMBAH PRODUK
     
     CATATAN:
     z-[9999] dibuat lebih tinggi dari bottom navigation
     pada layouts.app.
     
     max-height menggunakan 100dvh agar aman pada HP kecil.
========================================================== --}}

<div
    id="product-modal"
    class="hidden
           fixed
           inset-0
           z-[9999]
           bg-black/70
           backdrop-blur-sm
           p-2
           sm:p-4
           overflow-y-auto"
    role="dialog"
    aria-modal="true"
    aria-labelledby="product-modal-title"
>

    <div
        class="relative
               w-full
               max-w-lg
               mx-auto
               my-2
               sm:my-4
               bg-gray-800
               border border-white/15
               rounded-2xl
               shadow-2xl
               max-h-[calc(100dvh-1rem)]
               sm:max-h-[90vh]
               overflow-y-auto
               overscroll-contain
               pb-[calc(1rem+env(safe-area-inset-bottom))]"
        onclick="event.stopPropagation()"
    >

        {{-- =====================================================
             HEADER MODAL
        ====================================================== --}}

        <div
            class="sticky
                   top-0
                   z-20
                   flex
                   justify-between
                   items-center
                   px-4
                   sm:px-6
                   pt-4
                   pb-3
                   bg-gray-800/95
                   backdrop-blur-md
                   border-b
                   border-white/10"
        >

            <h3
                id="product-modal-title"
                class="text-lg
                       sm:text-xl
                       font-semibold
                       text-white
                       pr-3"
            >
                Tambah Produk Inventaris Anda!
            </h3>


            <button
                type="button"
                onclick="toggleProductModal(false)"
                class="shrink-0
                       text-gray-400
                       hover:text-white
                       text-sm
                       font-bold
                       w-9
                       h-9
                       flex
                       items-center
                       justify-center
                       rounded-lg
                       bg-gray-700/50
                       hover:bg-gray-700
                       transition"
                aria-label="Tutup modal"
            >
                ✕
            </button>

        </div>


        <div class="px-4 sm:px-6 pt-4">

            {{-- =================================================
                 PILIH CARA MENAMBAH PRODUK
            ================================================== --}}

            <div class="mb-4 sm:mb-5">

                <p class="text-[11px] text-gray-400 mb-2">
                    Pilih cara penambahan produk:
                </p>


                <div class="grid grid-cols-2 gap-2">

                    {{-- TAMBAH MANUAL --}}
                    <button
                        type="button"
                        class="w-full
                               bg-indigo-600/15
                               hover:bg-indigo-600/25
                               border border-indigo-500/30
                               text-indigo-300
                               rounded-xl
                               px-2.5
                               py-2.5
                               sm:px-3
                               sm:py-3
                               text-left
                               transition
                               ring-2
                               ring-indigo-500/20"
                    >

                        <div
                            class="text-sm
                                   sm:text-base
                                   font-semibold
                                   mb-0.5"
                        >
                            ➕ Tambah Manual
                        </div>

                        <div class="text-[10px] text-gray-400">
                            Input produk langsung
                        </div>

                    </button>


                    {{-- PEMBELIAN / RESTOCK --}}
                    <a
                        href="{{ route('purchase.create') }}"
                        class="w-full
                               bg-emerald-600/15
                               hover:bg-emerald-600/25
                               border border-emerald-500/30
                               text-emerald-300
                               rounded-xl
                               px-2.5
                               py-2.5
                               sm:px-3
                               sm:py-3
                               text-left
                               transition
                               block"
                    >

                        <div
                            class="text-sm
                                   sm:text-base
                                   font-semibold
                                   mb-0.5"
                        >
                            📦 Pembelian / Restock
                        </div>

                        <div class="text-[10px] text-gray-400">
                            Dari supplier & pembelian
                        </div>

                    </a>

                </div>

            </div>


            {{-- =================================================
                 FORM TAMBAH PRODUK
            ================================================== --}}

            <form
                action="{{ route('produk.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-3 sm:space-y-4"
                @if(!session('logged_in'))
                    onsubmit="return blockGuestAction(event, 'Simpan Produk')"
                @endif
            >

                @csrf


                {{-- KATEGORI --}}
                <div>

                    <label
                        class="block
                               text-xs
                               font-medium
                               text-gray-300
                               mb-1"
                    >
                        Kategori Produk
                    </label>

                    <input
                        type="text"
                        name="category"
                        placeholder="Contoh: Aksesoris / Sparepart"
                        class="w-full
                               bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-4
                               py-2.5
                               text-white
                               text-sm
                               focus:ring-2
                               focus:ring-indigo-500
                               outline-none"
                        required
                    >

                </div>


                {{-- BRAND --}}
                <div>

                    <label
                        class="block
                               text-xs
                               font-medium
                               text-gray-300
                               mb-1"
                    >
                        Merek / Brand (Opsional)
                    </label>

                    <input
                        type="text"
                        name="brand"
                        placeholder="Contoh: Vivan / Robot / Ori"
                        class="w-full
                               bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-4
                               py-2.5
                               text-white
                               text-sm
                               focus:ring-2
                               focus:ring-indigo-500
                               outline-none"
                    >

                </div>


                {{-- NAMA --}}
                <div>

                    <label
                        class="block
                               text-xs
                               font-medium
                               text-gray-300
                               mb-1"
                    >
                        Nama Barang / Layanan
                    </label>

                    <input
                        type="text"
                        name="name"
                        placeholder="Contoh: Kabel Data Fast Charging"
                        class="w-full
                               bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-4
                               py-2.5
                               text-white
                               text-sm
                               focus:ring-2
                               focus:ring-indigo-500
                               outline-none"
                        required
                    >

                </div>


                {{-- HARGA --}}
                <div class="grid grid-cols-2 gap-2 sm:gap-4">

                    <div>

                        <label
                            class="block
                                   text-xs
                                   font-medium
                                   text-gray-300
                                   mb-1"
                        >
                            Harga Modal (Rp)
                        </label>

                        <input
                            type="number"
                            name="capital_price"
                            placeholder="20000"
                            min="0"
                            inputmode="numeric"
                            class="w-full
                                   bg-gray-900
                                   border border-white/10
                                   rounded-xl
                                   px-3 sm:px-4
                                   py-2.5
                                   text-white
                                   text-sm
                                   focus:ring-2
                                   focus:ring-indigo-500
                                   outline-none"
                            required
                        >

                    </div>


                    <div>

                        <label
                            class="block
                                   text-xs
                                   font-medium
                                   text-gray-300
                                   mb-1"
                        >
                            Harga Jual (Rp)
                        </label>

                        <input
                            type="number"
                            name="price"
                            placeholder="35000"
                            min="0"
                            inputmode="numeric"
                            class="w-full
                                   bg-gray-900
                                   border border-white/10
                                   rounded-xl
                                   px-3 sm:px-4
                                   py-2.5
                                   text-white
                                   text-sm
                                   focus:ring-2
                                   focus:ring-indigo-500
                                   outline-none"
                            required
                        >

                    </div>

                </div>


                {{-- STOK --}}
                <div>

                    <label
                        class="block
                               text-xs
                               font-medium
                               text-gray-300
                               mb-1"
                    >
                        Stok Fisik / Jumlah Masuk
                    </label>

                    <input
                        type="number"
                        name="stock"
                        value="10"
                        min="0"
                        inputmode="numeric"
                        class="w-full
                               bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-4
                               py-2.5
                               text-white
                               text-sm
                               focus:ring-2
                               focus:ring-indigo-500
                               outline-none"
                        required
                    >

                </div>


                {{-- FOTO --}}
                <div>

                    <label
                        class="block
                               text-xs
                               font-medium
                               text-gray-300
                               mb-1"
                    >
                        Foto Produk / Servis
                    </label>

                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        class="w-full
                               bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-3
                               py-2
                               text-white
                               text-xs
                               file:mr-3
                               file:py-1.5
                               file:px-3
                               file:rounded-lg
                               file:border-0
                               file:text-xs
                               file:font-semibold
                               file:bg-indigo-600
                               file:text-white
                               hover:file:bg-indigo-500
                               outline-none"
                    >

                </div>


                {{-- =================================================
                     BUTTON FORM
                     
                     Sticky agar tetap mudah dijangkau ketika
                     form panjang di HP kecil.
                ================================================== --}}

                <div
                    class="sticky
                           bottom-0
                           z-20
                           flex
                           justify-end
                           gap-2
                           pt-3
                           mt-2
                           pb-1
                           bg-gray-800/95
                           backdrop-blur-md
                           border-t
                           border-white/10"
                >

                    <button
                        type="button"
                        onclick="toggleProductModal(false)"
                        class="bg-gray-700
                               hover:bg-gray-600
                               text-gray-300
                               px-4
                               py-2.5
                               rounded-xl
                               text-xs
                               font-medium
                               transition"
                    >
                        Batal
                    </button>


                    <button
                        type="submit"
                        class="bg-indigo-600
                               hover:bg-indigo-500
                               text-white
                               px-5
                               py-2.5
                               rounded-xl
                               text-xs
                               font-medium
                               transition
                               shadow"
                    >
                        Simpan Produk
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL EDIT PRODUK
========================================================== --}}

<div
    id="edit-product-modal"
    class="hidden
           fixed
           inset-0
           z-[9999]
           bg-black/70
           backdrop-blur-sm
           p-2
           sm:p-4
           overflow-y-auto"
    role="dialog"
    aria-modal="true"
    aria-labelledby="edit-product-modal-title"
>

    <div
        class="relative
               w-full
               max-w-lg
               mx-auto
               my-2
               sm:my-4
               bg-gray-800
               border border-white/15
               rounded-2xl
               shadow-2xl
               max-h-[calc(100dvh-1rem)]
               sm:max-h-[90vh]
               overflow-y-auto
               overscroll-contain
               pb-[calc(1rem+env(safe-area-inset-bottom))]"
        onclick="event.stopPropagation()"
    >

        {{-- HEADER --}}
        <div
            class="sticky
                   top-0
                   z-20
                   flex
                   justify-between
                   items-center
                   px-4
                   sm:px-6
                   pt-4
                   pb-3
                   bg-gray-800/95
                   backdrop-blur-md
                   border-b
                   border-white/10"
        >

            <h3
                id="edit-product-modal-title"
                class="text-lg
                       sm:text-xl
                       font-semibold
                       text-white
                       pr-3"
            >
                Edit Data Produk / Servis
            </h3>


            <button
                type="button"
                onclick="toggleEditModal(false)"
                class="shrink-0
                       text-gray-400
                       hover:text-white
                       text-sm
                       font-bold
                       w-9
                       h-9
                       flex
                       items-center
                       justify-center
                       rounded-lg
                       bg-gray-700/50
                       hover:bg-gray-700
                       transition"
                aria-label="Tutup modal edit"
            >
                ✕
            </button>

        </div>


        <div class="px-4 sm:px-6 pt-4">

            <form
                id="editProductForm"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-4"
                @if(!session('logged_in'))
                    onsubmit="return blockGuestAction(event, 'Simpan Perubahan')"
                @endif
            >

                @csrf
                @method('PUT')


                {{-- KATEGORI --}}
                <div>

                    <label
                        class="block
                               text-xs
                               font-medium
                               text-gray-300
                               mb-1"
                    >
                        Kategori Produk
                    </label>

                    <input
                        type="text"
                        id="edit_category"
                        name="category"
                        class="w-full
                               bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-4
                               py-2.5
                               text-white
                               text-sm
                               focus:ring-2
                               focus:ring-indigo-500
                               outline-none"
                        required
                    >

                </div>


                {{-- BRAND --}}
                <div>

                    <label
                        class="block
                               text-xs
                               font-medium
                               text-gray-300
                               mb-1"
                    >
                        Merek / Brand (Opsional)
                    </label>

                    <input
                        type="text"
                        id="edit_brand"
                        name="brand"
                        class="w-full
                               bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-4
                               py-2.5
                               text-white
                               text-sm
                               focus:ring-2
                               focus:ring-indigo-500
                               outline-none"
                    >

                </div>


                {{-- NAMA --}}
                <div>

                    <label
                        class="block
                               text-xs
                               font-medium
                               text-gray-300
                               mb-1"
                    >
                        Nama Barang / Layanan
                    </label>

                    <input
                        type="text"
                        id="edit_name"
                        name="name"
                        class="w-full
                               bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-4
                               py-2.5
                               text-white
                               text-sm
                               focus:ring-2
                               focus:ring-indigo-500
                               outline-none"
                        required
                    >

                </div>


                {{-- HARGA --}}
                <div class="grid grid-cols-2 gap-2 sm:gap-4">

                    <div>

                        <label
                            class="block
                                   text-xs
                                   font-medium
                                   text-gray-300
                                   mb-1"
                        >
                            Harga Modal (Rp)
                        </label>

                        <input
                            type="number"
                            id="edit_capital_price"
                            name="capital_price"
                            min="0"
                            inputmode="numeric"
                            class="w-full
                                   bg-gray-900
                                   border border-white/10
                                   rounded-xl
                                   px-3 sm:px-4
                                   py-2.5
                                   text-white
                                   text-sm
                                   focus:ring-2
                                   focus:ring-indigo-500
                                   outline-none"
                            required
                        >

                    </div>


                    <div>

                        <label
                            class="block
                                   text-xs
                                   font-medium
                                   text-gray-300
                                   mb-1"
                        >
                            Harga Jual (Rp)
                        </label>

                        <input
                            type="number"
                            id="edit_price"
                            name="price"
                            min="0"
                            inputmode="numeric"
                            class="w-full
                                   bg-gray-900
                                   border border-white/10
                                   rounded-xl
                                   px-3 sm:px-4
                                   py-2.5
                                   text-white
                                   text-sm
                                   focus:ring-2
                                   focus:ring-indigo-500
                                   outline-none"
                            required
                        >

                    </div>

                </div>


                {{-- STOK --}}
                <div>

                    <label
                        class="block
                               text-xs
                               font-medium
                               text-gray-300
                               mb-1"
                    >
                        Stok Fisik
                    </label>

                    <input
                        type="number"
                        id="edit_stock"
                        name="stock"
                        min="0"
                        inputmode="numeric"
                        class="w-full
                               bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-4
                               py-2.5
                               text-white
                               text-sm
                               focus:ring-2
                               focus:ring-indigo-500
                               outline-none"
                        required
                    >

                </div>


                {{-- FOTO --}}
                <div>

                    <label
                        class="block
                               text-xs
                               font-medium
                               text-gray-300
                               mb-1"
                    >
                        Ganti Foto Produk (Opsional)
                    </label>

                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        class="w-full
                               bg-gray-900
                               border border-white/10
                               rounded-xl
                               px-3
                               py-2
                               text-white
                               text-xs
                               file:mr-3
                               file:py-1.5
                               file:px-3
                               file:rounded-lg
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
                <div
                    class="sticky
                           bottom-0
                           z-20
                           flex
                           justify-end
                           gap-2
                           pt-3
                           mt-2
                           pb-1
                           bg-gray-800/95
                           backdrop-blur-md
                           border-t
                           border-white/10"
                >

                    <button
                        type="button"
                        onclick="toggleEditModal(false)"
                        class="bg-gray-700
                               hover:bg-gray-600
                               text-gray-300
                               px-4
                               py-2.5
                               rounded-xl
                               text-xs
                               font-medium
                               transition"
                    >
                        Batal
                    </button>


                    <button
                        type="submit"
                        class="bg-amber-600
                               hover:bg-amber-500
                               text-white
                               px-5
                               py-2.5
                               rounded-xl
                               text-xs
                               font-medium
                               transition
                               shadow"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- =========================================================
     JAVASCRIPT
========================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    const productModal =
        document.getElementById('product-modal');

    const editProductModal =
        document.getElementById('edit-product-modal');

    const dateFilterSelect =
        document.getElementById('dateFilterSelect');

    const customDateInputs =
        document.getElementById('customDateInputs');


    /*
    |--------------------------------------------------------------------------
    | MODAL BODY SCROLL LOCK
    |--------------------------------------------------------------------------
    */

    function updateBodyScrollLock() {

        const productOpen =
            productModal &&
            !productModal.classList.contains('hidden');

        const editOpen =
            editProductModal &&
            !editProductModal.classList.contains('hidden');

        document.body.classList.toggle(
            'overflow-hidden',
            productOpen || editOpen
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MODAL TAMBAH PRODUK
    |--------------------------------------------------------------------------
    */

    window.toggleProductModal = function (forceState = null) {

        if (!productModal) {
            return;
        }

        let shouldOpen;

        if (forceState === true) {

            shouldOpen = true;

        } else if (forceState === false) {

            shouldOpen = false;

        } else {

            shouldOpen =
                productModal.classList.contains('hidden');

        }


        if (shouldOpen) {

            /*
             * Tutup modal edit jika sedang terbuka.
             */
            if (editProductModal) {
                editProductModal.classList.add('hidden');
            }

            productModal.classList.remove('hidden');

            updateBodyScrollLock();

            /*
             * Scroll modal ke bagian paling atas.
             */
            const modalContent =
                productModal.querySelector(
                    ':scope > div'
                );

            if (modalContent) {
                modalContent.scrollTop = 0;
            }

        } else {

            productModal.classList.add('hidden');

            updateBodyScrollLock();

        }

    };


    /*
    |--------------------------------------------------------------------------
    | MODAL EDIT
    |--------------------------------------------------------------------------
    */

    window.toggleEditModal = function (forceState = null) {

        if (!editProductModal) {
            return;
        }

        let shouldOpen;

        if (forceState === true) {

            shouldOpen = true;

        } else if (forceState === false) {

            shouldOpen = false;

        } else {

            shouldOpen =
                editProductModal.classList.contains('hidden');

        }


        if (shouldOpen) {

            /*
             * Tutup modal tambah jika sedang terbuka.
             */
            if (productModal) {
                productModal.classList.add('hidden');
            }

            editProductModal.classList.remove('hidden');

            updateBodyScrollLock();

            const modalContent =
                editProductModal.querySelector(
                    ':scope > div'
                );

            if (modalContent) {
                modalContent.scrollTop = 0;
            }

        } else {

            editProductModal.classList.add('hidden');

            updateBodyScrollLock();

        }

    };


    /*
    |--------------------------------------------------------------------------
    | BUKA MODAL EDIT PRODUK
    |--------------------------------------------------------------------------
    */

    window.openEditModal = function (
        id,
        name,
        category,
        brand,
        capital_price,
        price,
        stock
    ) {

        const form =
            document.getElementById('editProductForm');

        if (!form) {
            return;
        }


        /*
         * Action form edit.
         */
        form.action =
            "{{ url('produk') }}/" + id;


        /*
         * Isi field.
         */
        const nameInput =
            document.getElementById('edit_name');

        const categoryInput =
            document.getElementById('edit_category');

        const brandInput =
            document.getElementById('edit_brand');

        const capitalInput =
            document.getElementById('edit_capital_price');

        const priceInput =
            document.getElementById('edit_price');

        const stockInput =
            document.getElementById('edit_stock');


        if (nameInput) {
            nameInput.value =
                name ?? '';
        }


        if (categoryInput) {
            categoryInput.value =
                category ?? '';
        }


        if (brandInput) {
            brandInput.value =
                brand ?? '';
        }


        if (capitalInput) {
            capitalInput.value =
                capital_price ?? 0;
        }


        if (priceInput) {
            priceInput.value =
                price ?? 0;
        }


        if (stockInput) {
            stockInput.value =
                stock ?? 0;
        }


        /*
         * Buka modal.
         */
        window.toggleEditModal(true);

    };


    /*
    |--------------------------------------------------------------------------
    | GUEST ACTION
    |--------------------------------------------------------------------------
    */

    window.blockGuestAction = function (
        event,
        actionName
    ) {

        event.preventDefault();

        alert(
            '🔒 Fitur ini membutuhkan akun.\n\n' +
            'Untuk menggunakan fitur ini, silakan daftar akun terlebih dahulu ' +
            'atau hubungi kontak yang tersedia.'
        );

        return false;

    };


    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    window.handleDateFilterChange = function (value) {

        if (!customDateInputs) {
            return;
        }


        if (value === 'custom') {

            customDateInputs.classList.remove('hidden');

        } else {

            customDateInputs.classList.add('hidden');

            const form =
                document.getElementById('dateFilterForm');

            if (form) {
                form.submit();
            }

        }

    };


    /*
    |--------------------------------------------------------------------------
    | SAAT HALAMAN DIBUKA
    |--------------------------------------------------------------------------
    */

    if (
        dateFilterSelect &&
        customDateInputs
    ) {

        if (
            dateFilterSelect.value === 'custom'
        ) {

            customDateInputs.classList.remove(
                'hidden'
            );

        } else {

            customDateInputs.classList.add(
                'hidden'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | KLIK BACKDROP UNTUK MENUTUP MODAL
    |--------------------------------------------------------------------------
    */

    if (productModal) {

        productModal.addEventListener(
            'click',
            function (event) {

                if (
                    event.target === productModal
                ) {

                    window.toggleProductModal(false);

                }

            }
        );

    }


    if (editProductModal) {

        editProductModal.addEventListener(
            'click',
            function (event) {

                if (
                    event.target === editProductModal
                ) {

                    window.toggleEditModal(false);

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | TOMBOL ESCAPE
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key !== 'Escape'
            ) {
                return;
            }


            if (
                productModal &&
                !productModal.classList.contains('hidden')
            ) {

                window.toggleProductModal(false);

                return;

            }


            if (
                editProductModal &&
                !editProductModal.classList.contains('hidden')
            ) {

                window.toggleEditModal(false);

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | RESET BODY SCROLL JIKA HALAMAN DIRELOAD
    |--------------------------------------------------------------------------
    */

    updateBodyScrollLock();

});

</script>

@endsection