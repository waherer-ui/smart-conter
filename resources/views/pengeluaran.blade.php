@extends('layouts.app')

@section('title', 'Pengeluaran')
@section('header', 'Manajemen Pengeluaran')

@section('content')

<div class="space-y-6">{{-- =========================================================
     RINGKASAN PENGELUARAN
========================================================== --}}

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

    {{-- Hari Ini --}}
    <div class="bg-gray-800 p-5 rounded-xl border border-white/10">
        <div class="flex items-center justify-between">

            <div>
                <p class="text-sm text-gray-400">
                    Pengeluaran Hari Ini
                </p>

                <h3 class="text-2xl font-bold text-white mt-1">
                    Rp {{ number_format($totalHariIni ?? 0, 0, ',', '.') }}
                </h3>
            </div>

            <div class="text-2xl">
                💸
            </div>

        </div>
    </div>


    {{-- Bulan Ini --}}
    <div class="bg-gray-800 p-5 rounded-xl border border-white/10">
        <div class="flex items-center justify-between">

            <div>
                <p class="text-sm text-gray-400">
                    Pengeluaran Bulan Ini
                </p>

                <h3 class="text-2xl font-bold text-white mt-1">
                    Rp {{ number_format($totalBulanIni ?? 0, 0, ',', '.') }}
                </h3>
            </div>

            <div class="text-2xl">
                📅
            </div>

        </div>
    </div>


    {{-- Total Filter --}}
    <div class="bg-gray-800 p-5 rounded-xl border border-white/10 sm:col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between">

            <div>
                <p class="text-sm text-gray-400">
                    Total Pengeluaran
                </p>

                <h3 class="text-2xl font-bold text-white mt-1">
                    Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                </h3>
            </div>

            <div class="text-2xl">
                📊
            </div>

        </div>
    </div>

</div>



{{-- =========================================================
     FORM TAMBAH PENGELUARAN
========================================================== --}}

<div class="bg-gray-800 rounded-xl border border-white/10 overflow-hidden">

    <div class="p-5 border-b border-white/10">

        <div>
            <h2 class="text-lg font-semibold text-white">
                Tambah Pengeluaran
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Catat biaya operasional toko.
            </p>
        </div>

    </div>


    <div class="p-5">

        {{-- Error Validasi --}}
        @if($errors->any())

            <div class="mb-5 p-4 rounded-xl
                        bg-red-500/10
                        border border-red-500/20
                        text-red-400">

                <p class="font-medium mb-2">
                    Terjadi kesalahan:
                </p>

                <ul class="list-disc list-inside space-y-1 text-sm">

                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('pengeluaran.store') }}"
            class="grid grid-cols-1 md:grid-cols-2 gap-4"
        >

            @csrf


            {{-- Kategori --}}
            <div>

                <label
                    for="category"
                    class="block text-sm font-medium text-gray-400 mb-2"
                >
                    Kategori
                </label>

                <select
                    id="category"
                    name="category"
                    required
                    class="w-full bg-gray-900
                           border border-white/10
                           rounded-lg
                           px-4 py-2.5
                           text-white
                           focus:outline-none
                           focus:border-blue-500"
                >

                    <option value="">
                        Pilih kategori
                    </option>

                    <option value="Listrik"
                        {{ old('category') === 'Listrik' ? 'selected' : '' }}>
                        Listrik
                    </option>

                    <option value="Air"
                        {{ old('category') === 'Air' ? 'selected' : '' }}>
                        Air
                    </option>

                    <option value="Internet"
                        {{ old('category') === 'Internet' ? 'selected' : '' }}>
                        Internet
                    </option>

                    <option value="Sewa"
                        {{ old('category') === 'Sewa' ? 'selected' : '' }}>
                        Sewa
                    </option>

                    <option value="Gaji"
                        {{ old('category') === 'Gaji' ? 'selected' : '' }}>
                        Gaji
                    </option>

                    <option value="Transportasi"
                        {{ old('category') === 'Transportasi' ? 'selected' : '' }}>
                        Transportasi
                    </option>

                    <option value="Perlengkapan"
                        {{ old('category') === 'Perlengkapan' ? 'selected' : '' }}>
                        Perlengkapan
                    </option>

                    <option value="Operasional"
                        {{ old('category') === 'Operasional' ? 'selected' : '' }}>
                        Operasional
                    </option>

                    <option value="Lainnya"
                        {{ old('category') === 'Lainnya' ? 'selected' : '' }}>
                        Lainnya
                    </option>

                </select>

            </div>


            {{-- Tanggal --}}
            <div>

                <label
                    for="expense_date"
                    class="block text-sm font-medium text-gray-400 mb-2"
                >
                    Tanggal
                </label>

                <input
                    type="date"
                    id="expense_date"
                    name="expense_date"
                    value="{{ old('expense_date', date('Y-m-d')) }}"
                    required
                    class="w-full bg-gray-900
                           border border-white/10
                           rounded-lg
                           px-4 py-2.5
                           text-white
                           focus:outline-none
                           focus:border-blue-500"
                >

            </div>


            {{-- Jumlah --}}
            <div>

                <label
                    for="amount"
                    class="block text-sm font-medium text-gray-400 mb-2"
                >
                    Jumlah Pengeluaran
                </label>

                <div class="relative">

                    <span class="absolute left-4 top-1/2
                                 -translate-y-1/2
                                 text-gray-500 text-sm">
                        Rp
                    </span>

                    <input
                        type="number"
                        id="amount"
                        name="amount"
                        value="{{ old('amount') }}"
                        min="1"
                        step="0.01"
                        placeholder="250000"
                        required
                        class="w-full bg-gray-900
                               border border-white/10
                               rounded-lg
                               pl-11 pr-4
                               py-2.5
                               text-white
                               focus:outline-none
                               focus:border-blue-500"
                    >

                </div>

            </div>


            {{-- Keterangan --}}
            <div>

                <label
                    for="description"
                    class="block text-sm font-medium text-gray-400 mb-2"
                >
                    Keterangan
                </label>

                <input
                    type="text"
                    id="description"
                    name="description"
                    value="{{ old('description') }}"
                    placeholder="Contoh: Pembayaran listrik toko"
                    class="w-full bg-gray-900
                           border border-white/10
                           rounded-lg
                           px-4 py-2.5
                           text-white
                           focus:outline-none
                           focus:border-blue-500"
                >

            </div>


            {{-- Tombol --}}
            <div class="md:col-span-2 flex justify-end pt-2">

                <button
                    type="submit"
                    class="w-full sm:w-auto
                           bg-blue-600
                           hover:bg-blue-500
                           text-white
                           font-medium
                           rounded-lg
                           px-5 py-2.5
                           transition
                           shadow"
                >
                    + Tambah Pengeluaran
                </button>

            </div>

        </form>

    </div>

</div>



{{-- =========================================================
     FILTER
========================================================== --}}

<div class="bg-gray-800 rounded-xl border border-white/10 overflow-hidden">

    <div class="p-5 border-b border-white/10">

        <h2 class="text-lg font-semibold text-white">
            Filter Pengeluaran
        </h2>

        <p class="text-sm text-gray-400 mt-1">
            Gunakan filter untuk mencari pengeluaran tertentu.
        </p>

    </div>


    <div class="p-5">

        <form
            method="GET"
            action="{{ route('pengeluaran') }}"
            class="grid grid-cols-1 md:grid-cols-3 gap-4"
        >

            {{-- Dari Tanggal --}}
            <div>

                <label
                    for="start_date"
                    class="block text-sm font-medium text-gray-400 mb-2"
                >
                    Dari Tanggal
                </label>

                <input
                    type="date"
                    id="start_date"
                    name="start_date"
                    value="{{ request('start_date') }}"
                    class="w-full bg-gray-900
                           border border-white/10
                           rounded-lg
                           px-4 py-2.5
                           text-white
                           focus:outline-none
                           focus:border-blue-500"
                >

            </div>


            {{-- Sampai Tanggal --}}
            <div>

                <label
                    for="end_date"
                    class="block text-sm font-medium text-gray-400 mb-2"
                >
                    Sampai Tanggal
                </label>

                <input
                    type="date"
                    id="end_date"
                    name="end_date"
                    value="{{ request('end_date') }}"
                    class="w-full bg-gray-900
                           border border-white/10
                           rounded-lg
                           px-4 py-2.5
                           text-white
                           focus:outline-none
                           focus:border-blue-500"
                >

            </div>


            {{-- Kategori --}}
            <div>

                <label
                    for="filter_category"
                    class="block text-sm font-medium text-gray-400 mb-2"
                >
                    Kategori
                </label>

                <select
                    id="filter_category"
                    name="category"
                    class="w-full bg-gray-900
                           border border-white/10
                           rounded-lg
                           px-4 py-2.5
                           text-white
                           focus:outline-none
                           focus:border-blue-500"
                >

                    <option value="">
                        Semua kategori
                    </option>

                    @foreach($kategori ?? [] as $item)

                        <option
                            value="{{ $item }}"
                            {{ request('category') === $item ? 'selected' : '' }}
                        >
                            {{ $item }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Tombol Filter --}}
            <div class="md:col-span-3 flex flex-col sm:flex-row justify-end gap-2 pt-2">

                <a
                    href="{{ route('pengeluaran') }}"
                    class="text-center
                           bg-gray-700
                           hover:bg-gray-600
                           text-white
                           font-medium
                           rounded-lg
                           px-5 py-2.5
                           transition"
                >
                    Reset
                </a>

                <button
                    type="submit"
                    class="bg-blue-600
                           hover:bg-blue-500
                           text-white
                           font-medium
                           rounded-lg
                           px-5 py-2.5
                           transition"
                >
                    🔍 Filter
                </button>

            </div>

        </form>

    </div>

</div>



{{-- =========================================================
     RIWAYAT PENGELUARAN
========================================================== --}}

<div class="bg-gray-800 rounded-xl border border-white/10 overflow-hidden">

    <div class="p-5 border-b border-white/10">

        <div class="flex flex-col sm:flex-row
                    sm:items-center
                    sm:justify-between
                    gap-2">

            <div>

                <h2 class="text-lg font-semibold text-white">
                    Riwayat Pengeluaran
                </h2>

                <p class="text-sm text-gray-400 mt-1">
                    Daftar biaya operasional yang telah dicatat.
                </p>

            </div>

            <div class="text-sm text-gray-500">
                {{ count($pengeluaran ?? []) }} data
            </div>

        </div>

    </div>


    {{-- =====================================================
         DESKTOP TABLE
    ====================================================== --}}

    <div class="hidden md:block overflow-x-auto">

        <table class="w-full text-sm text-left">

            <thead class="bg-gray-900 text-gray-400">

                <tr>

                    <th class="px-5 py-3 whitespace-nowrap">
                        No
                    </th>

                    <th class="px-5 py-3 whitespace-nowrap">
                        Tanggal
                    </th>

                    <th class="px-5 py-3 whitespace-nowrap">
                        Kategori
                    </th>

                    <th class="px-5 py-3">
                        Keterangan
                    </th>

                    <th class="px-5 py-3 whitespace-nowrap">
                        Jumlah
                    </th>

                    <th class="px-5 py-3 whitespace-nowrap">
                        Dicatat Oleh
                    </th>

                    <th class="px-5 py-3 text-center whitespace-nowrap">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-white/10">

                @forelse($pengeluaran ?? [] as $index => $item)

                    <tr class="hover:bg-white/5 transition">

                        <td class="px-5 py-3 text-gray-500">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-5 py-3 text-gray-400 whitespace-nowrap">
                            {{ $item->expense_date?->format('d/m/Y') ?? '-' }}
                        </td>

                        <td class="px-5 py-3">

                            <span class="inline-flex
                                         items-center
                                         rounded-full
                                         bg-blue-500/10
                                         px-2.5 py-1
                                         text-xs
                                         font-medium
                                         text-blue-400">
                                {{ $item->category }}
                            </span>

                        </td>

                        <td class="px-5 py-3 text-gray-400">
                            {{ $item->description ?: '-' }}
                        </td>

                        <td class="px-5 py-3 text-white font-semibold whitespace-nowrap">
                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </td>

                        <td class="px-5 py-3 text-gray-400 whitespace-nowrap">
                            {{ $item->user?->name ?? '-' }}
                        </td>

                        <td class="px-5 py-3 text-center">

                            @if(session('user_role') === 'admin')

                                <div class="flex justify-center gap-2">

                                    {{-- Edit --}}
                                    <a
                                        href="{{ route('pengeluaran.edit', $item->id) }}"
                                        class="px-3 py-1.5
                                               rounded-lg
                                               bg-yellow-600
                                               hover:bg-yellow-500
                                               text-white
                                               text-xs
                                               font-medium
                                               transition"
                                    >
                                        Edit
                                    </a>


                                    {{-- Hapus --}}
                                    <form
                                        method="POST"
                                        action="{{ route('pengeluaran.destroy', $item->id) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus pengeluaran ini?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="px-3 py-1.5
                                                   rounded-lg
                                                   bg-red-600
                                                   hover:bg-red-500
                                                   text-white
                                                   text-xs
                                                   font-medium
                                                   transition"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            @else

                                <span class="text-xs text-gray-600">
                                    —
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="px-5 py-12 text-center"
                        >

                            <div class="text-4xl mb-3">
                                💸
                            </div>

                            <p class="text-gray-400">
                                Belum ada data pengeluaran.
                            </p>

                            <p class="text-sm text-gray-600 mt-1">
                                Pengeluaran yang ditambahkan akan muncul di sini.
                            </p>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>



    {{-- =====================================================
         MOBILE CARD
    ====================================================== --}}

    <div class="md:hidden divide-y divide-white/10">

        @forelse($pengeluaran ?? [] as $index => $item)

            <div class="p-4">

                <div class="flex items-start justify-between gap-3">

                    <div class="min-w-0">

                        <div class="flex items-center gap-2">

                            <span class="text-xs text-gray-500">
                                #{{ $index + 1 }}
                            </span>

                            <span class="inline-flex
                                         rounded-full
                                         bg-blue-500/10
                                         px-2.5 py-1
                                         text-xs
                                         font-medium
                                         text-blue-400">
                                {{ $item->category }}
                            </span>

                        </div>

                        <p class="text-white font-semibold mt-2">
                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                        </p>

                    </div>


                    <div class="text-right text-xs text-gray-500 whitespace-nowrap">
                        {{ $item->expense_date?->format('d/m/Y') ?? '-' }}
                    </div>

                </div>


                <div class="mt-3 space-y-1">

                    <p class="text-sm text-gray-400">

                        <span class="text-gray-500">
                            Keterangan:
                        </span>

                        {{ $item->description ?: '-' }}

                    </p>

                    <p class="text-sm text-gray-400">

                        <span class="text-gray-500">
                            Dicatat oleh:
                        </span>

                        {{ $item->user?->name ?? '-' }}

                    </p>

                </div>


                {{-- Aksi Admin --}}
                @if(session('user_role') === 'admin')

                    <div class="flex gap-2 mt-4">

                        <a
                            href="{{ route('pengeluaran.edit', $item->id) }}"
                            class="flex-1
                                   text-center
                                   px-3 py-2
                                   rounded-lg
                                   bg-yellow-600
                                   hover:bg-yellow-500
                                   text-white
                                   text-xs
                                   font-medium
                                   transition"
                        >
                            Edit
                        </a>


                        <form
                            method="POST"
                            action="{{ route('pengeluaran.destroy', $item->id) }}"
                            class="flex-1"
                            onsubmit="return confirm('Yakin ingin menghapus pengeluaran ini?')"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="w-full
                                       px-3 py-2
                                       rounded-lg
                                       bg-red-600
                                       hover:bg-red-500
                                       text-white
                                       text-xs
                                       font-medium
                                       transition"
                            >
                                Hapus
                            </button>

                        </form>

                    </div>

                @endif

            </div>

        @empty

            <div class="px-5 py-12 text-center">

                <div class="text-4xl mb-3">
                    💸
                </div>

                <p class="text-gray-400">
                    Belum ada data pengeluaran.
                </p>

                <p class="text-sm text-gray-600 mt-1">
                    Pengeluaran yang ditambahkan akan muncul di sini.
                </p>

            </div>

        @endforelse

    </div>

</div>

</div>@endsection