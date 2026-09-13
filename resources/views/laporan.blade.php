@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('header', 'Rekap Laporan Keuangan')

@section('content')

<div class="space-y-6">

    {{-- =========================================================
         CEK ROLE
    ========================================================== --}}

    @php
        $isAdmin = session('user_role') === 'admin';
    @endphp


    {{-- =========================================================
         RINGKASAN HARI INI
    ========================================================== --}}
    <div class="mb-5">

            <h2 class="text-lg font-semibold text-white">
                Ringkasan Hari Ini
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                {{ $startDate->format('d/m/Y') }}
                -
                {{ $endDate->format('d/m/Y') }}
            </p>

        </div>

    <div class="grid grid-cols-2 md:grid-cols-2
                {{ $isAdmin ? 'lg:grid-cols-6' : 'lg:grid-cols-3' }}
                gap-1">
      


        {{-- =====================================================
             ADMIN
        ====================================================== --}}

        @if($isAdmin)

            {{-- Pendapatan --}}
            <div class="bg-gray-800 p-5 rounded-xl border border-white/10">

                <p class="text-sm text-gray-400">
                    Pendapatan Hari Ini
                </p>

                <h3 class="text-2xl font-bold text-white mt-1">
                    Rp {{ number_format($totalPendapatanHariIni ?? 0, 0, ',', '.') }}
                </h3>

            </div>


            {{-- HPP --}}
            <div class="bg-gray-800 p-5 rounded-xl border border-white/10">

                <p class="text-sm text-gray-400">
                    HPP Hari Ini
                </p>

                <h3 class="text-2xl font-bold text-white mt-1">
                    Rp {{ number_format($hppHariIni ?? 0, 0, ',', '.') }}
                </h3>

            </div>


            {{-- Laba Kotor --}}
            <div class="bg-gray-800 p-5 rounded-xl border border-white/10">

                <p class="text-sm text-gray-400">
                    Laba Kotor Hari Ini
                </p>

                <h3 class="text-2xl font-bold mt-1
                    {{ ($labaKotorHariIni ?? 0) >= 0
                        ? 'text-green-400'
                        : 'text-red-400' }}">

                    Rp {{ number_format($labaKotorHariIni ?? 0, 0, ',', '.') }}

                </h3>

            </div>


            {{-- Pengeluaran --}}
            <div class="bg-gray-800 p-5 rounded-xl border border-white/10">

                <p class="text-sm text-gray-400">
                    Pengeluaran Hari Ini
                </p>

                <h3 class="text-2xl font-bold text-white mt-1">
                    Rp {{ number_format($totalPengeluaranHariIni ?? 0, 0, ',', '.') }}
                </h3>

            </div>


            {{-- Setoran --}}
            <div class="bg-gray-800 p-5 rounded-xl border border-white/10">

                <p class="text-sm text-gray-400">
                    Setoran Hari Ini
                </p>

                <h3 class="text-2xl font-bold mt-1
                    {{ ($kasBersihHariIni ?? 0) >= 0
                        ? 'text-green-400'
                        : 'text-red-400' }}">

                    Rp {{ number_format($kasBersihHariIni ?? 0, 0, ',', '.') }}

                </h3>

            </div>


            {{-- Laba Bersih --}}
            <div class="bg-gray-800 p-5 rounded-xl border border-white/10">

                <p class="text-sm text-gray-400">
                    Laba Bersih Hari Ini
                </p>

                <h3 class="text-2xl font-bold mt-1
                    {{ ($labaBersihHariIni ?? 0) >= 0
                        ? 'text-green-400'
                        : 'text-red-400' }}">

                    Rp {{ number_format($labaBersihHariIni ?? 0, 0, ',', '.') }}

                </h3>

            </div>


        @else


            {{-- =================================================
                 KASIR
            ================================================== --}}

            {{-- Pendapatan --}}
            <div class="bg-gray-800 p-5 rounded-xl border border-white/10">

                <p class="text-sm text-gray-400">
                    Pendapatan Hari Ini
                </p>

                <h3 class="text-2xl font-bold text-white mt-1">
                    Rp {{ number_format($totalPendapatanHariIni ?? 0, 0, ',', '.') }}
                </h3>

            </div>


            {{-- Pengeluaran --}}
            <div class="bg-gray-800 p-5 rounded-xl border border-white/10">

                <p class="text-sm text-gray-400">
                    Pengeluaran Hari Ini
                </p>

                <h3 class="text-2xl font-bold text-white mt-1">
                    Rp {{ number_format($totalPengeluaranHariIni ?? 0, 0, ',', '.') }}
                </h3>

            </div>


            {{-- Setoran --}}
            <div class="bg-gray-800 p-5 rounded-xl border border-white/10">

                <p class="text-sm text-gray-400">
                    Setoran Hari Ini
                </p>

                <h3 class="text-2xl font-bold mt-1
                    {{ ($kasBersihHariIni ?? 0) >= 0
                        ? 'text-green-400'
                        : 'text-red-400' }}">

                    Rp {{ number_format($kasBersihHariIni ?? 0, 0, ',', '.') }}

                </h3>

            </div>

        @endif

    </div>



{{-- =========================================================
     FILTER LAPORAN
========================================================== --}}

<div class="bg-gray-800 p-5 rounded-xl border border-white/10">

    <div class="mb-2">

        <h2 class="text-lg font-semibold text-white">
            Filter Laporan
        </h2>

        <p class="text-sm text-gray-400 mt-1">
            @if($isAdmin)
                Pilih kasir dan periode untuk melihat laporan.
            @else
                Pilih periode untuk melihat laporan Anda.
            @endif
        </p>

    </div>


    <form
        method="GET"
        action="{{ route('laporan') }}"
        class="grid grid-cols-1
               {{ $isAdmin ? 'md:grid-cols-4' : 'md:grid-cols-3' }}
               gap-2 sm:gap-4"
    >

        {{-- =====================================================
             FILTER KASIR
             HANYA ADMIN
        ====================================================== --}}

        @if($isAdmin)

            <div>

                <label
                    for="cashier_id"
                    class="block text-sm text-gray-400 mb-2"
                >
                    Kasir
                </label>

                <select
                    id="cashier_id"
                    name="cashier_id"
                    class="w-full bg-gray-900 border border-white/10
                           rounded-lg px-4 py-0.5 sm:py-2.5 text-white
                           focus:outline-none focus:border-blue-500"
                >

                    <option value="">
                        Semua Kasir
                    </option>

                    @foreach($kasir as $item)

                        <option
                            value="{{ $item->id }}"
                            {{ (string) request('cashier_id') === (string) $item->id
                                ? 'selected'
                                : '' }}
                        >
                            {{ $item->name }}
                        </option>

                    @endforeach

                </select>

            </div>

        @endif


        {{-- =====================================================
             DARI TANGGAL
        ====================================================== --}}

        <div>

            <label
                for="start_date"
                class="block text-sm text-gray-400 mb-2"
            >
                Dari Tanggal
            </label>

            <input
                type="date"
                id="start_date"
                name="start_date"
                value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                class="w-full bg-gray-900 border border-white/10
                       rounded-lg px-2 py-0.5 sm:py-2.5 text-white
                       focus:outline-none focus:border-blue-500"
            >

        </div>


        {{-- =====================================================
             SAMPAI TANGGAL
        ====================================================== --}}

        <div>

            <label
                for="end_date"
                class="block text-sm text-gray-400 mb-2"
            >
                Sampai Tanggal
            </label>

            <input
                type="date"
                id="end_date"
                name="end_date"
                value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                class="w-full bg-gray-900 border border-white/10
                       rounded-lg px-2 py-0.5 sm:py-2.5 text-white
                       focus:outline-none focus:border-blue-500"
            >

        </div>


        {{-- =====================================================
             TOMBOL
        ====================================================== --}}

        <div class="flex items-end gap-1">

            <button
                type="submit"
                class="flex-1 bg-blue-600 hover:bg-blue-700
                       text-white font-medium rounded-lg
                       px-2 sm:px-4 py-1 sm:py-2.5 transition
                       text-xs sm:text-sm"
            >
                Tampilkan Laporan
            </button>

            <a
                href="{{ route('laporan') }}"
                class="bg-gray-700 hover:bg-gray-600
                       text-white font-medium rounded-lg
                       px-2 sm:px-4 py-2.5 transition
                       text-xs sm:text-sm"
            >
                Reset
            </a>

        </div>

    </form>

</div>



    {{-- =========================================================
         RINGKASAN PERIODE
    ========================================================== --}}

    <div class="bg-gray-800 p-5 rounded-xl border border-white/10">

        <div class="mb-5">

            <h2 class="text-lg font-semibold text-white">
                Ringkasan Periode
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                {{ $startDate->format('d/m/Y') }}
                -
                {{ $endDate->format('d/m/Y') }}
            </p>

        </div>


        {{-- =====================================================
             ADMIN
        ====================================================== --}}

        @if($isAdmin)

            <div class="grid grid-cols-2 md:grid-cols-2
                        lg:grid-cols-4 gap-1">


                {{-- Omzet --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Omzet Penjualan
                    </p>

                    <p class="text-xl font-bold text-white mt-1">
                        Rp {{ number_format($omzet ?? 0, 0, ',', '.') }}
                    </p>

                </div>


                {{-- HPP --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        HPP / Modal Barang
                    </p>

                    <p class="text-xl font-bold text-white mt-1">
                        Rp {{ number_format($hpp ?? 0, 0, ',', '.') }}
                    </p>

                </div>


                {{-- Laba Kotor --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Laba Kotor
                    </p>

                    <p class="text-xl font-bold mt-1
                        {{ ($labaKotor ?? 0) >= 0
                            ? 'text-green-400'
                            : 'text-red-400' }}">

                        Rp {{ number_format($labaKotor ?? 0, 0, ',', '.') }}

                    </p>

                </div>


                {{-- Pengeluaran --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Total Pengeluaran
                    </p>

                    <p class="text-xl font-bold text-white mt-1">
                        Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                    </p>

                </div>


                {{-- Laba Bersih --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Laba Bersih
                    </p>

                    <p class="text-xl font-bold mt-1
                        {{ ($labaBersih ?? 0) >= 0
                            ? 'text-green-400'
                            : 'text-red-400' }}">

                        Rp {{ number_format($labaBersih ?? 0, 0, ',', '.') }}

                    </p>

                </div>


                {{-- Total Transaksi --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Total Transaksi
                    </p>

                    <p class="text-xl font-bold text-white mt-1">
                        {{ $totalTransaksi ?? 0 }} Transaksi
                    </p>

                </div>


                {{-- Barang Terjual --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Barang Terjual
                    </p>

                    <p class="text-xl font-bold text-white mt-1">
                        {{ $barangTerjual ?? 0 }} Pcs
                    </p>

                </div>


                {{-- Total Diskon --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Total Diskon
                    </p>

                    <p class="text-xl font-bold text-white mt-1">
                        Rp {{ number_format($totalDiskon ?? 0, 0, ',', '.') }}
                    </p>

                </div>

            </div>


        @else


            {{-- =================================================
                 KASIR
            ================================================== --}}

            <div class="grid grid-cols-1 md:grid-cols-2
                        lg:grid-cols-3 gap-4">


                {{-- Omzet --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Omzet Penjualan
                    </p>

                    <p class="text-xl font-bold text-white mt-1">
                        Rp {{ number_format($omzet ?? 0, 0, ',', '.') }}
                    </p>

                </div>


                {{-- Pengeluaran --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Total Pengeluaran
                    </p>

                    <p class="text-xl font-bold text-white mt-1">
                        Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                    </p>

                </div>


                {{-- Setoran --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Setoran Periode
                    </p>

                    <p class="text-xl font-bold mt-1
                        {{ ($kasBersih ?? 0) >= 0
                            ? 'text-green-400'
                            : 'text-red-400' }}">

                        Rp {{ number_format($kasBersih ?? 0, 0, ',', '.') }}

                    </p>

                </div>


                {{-- Total Transaksi --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Total Transaksi
                    </p>

                    <p class="text-xl font-bold text-white mt-1">
                        {{ $totalTransaksi ?? 0 }} Transaksi
                    </p>

                </div>


                {{-- Barang Terjual --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Barang Terjual
                    </p>

                    <p class="text-xl font-bold text-white mt-1">
                        {{ $barangTerjual ?? 0 }} Pcs
                    </p>

                </div>


                {{-- Total Diskon --}}
                <div class="bg-gray-900 rounded-lg p-4">

                    <p class="text-sm text-gray-400">
                        Total Diskon
                    </p>

                    <p class="text-xl font-bold text-white mt-1">
                        Rp {{ number_format($totalDiskon ?? 0, 0, ',', '.') }}
                    </p>

                </div>

            </div>

        @endif

    </div>



    {{-- =========================================================
         RIWAYAT PENJUALAN
    ========================================================== --}}

    <div class="bg-gray-800 rounded-xl border border-white/10 overflow-hidden">

        <div class="p-5 border-b border-white/10">

            <h2 class="text-lg font-semibold text-white">
                Riwayat Penjualan
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Daftar transaksi berdasarkan periode yang dipilih.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-gray-900 text-gray-400">

                    <tr>

                        <th class="px-5 py-3">
                            No
                        </th>

                        <th class="px-5 py-3">
                            No. Transaksi
                        </th>

                        <th class="px-5 py-3">
                            Tanggal
                        </th>

                        <th class="px-5 py-3">
                            Kasir
                        </th>

                        <th class="px-5 py-3">
                            Total
                        </th>

                        <th class="px-5 py-3">
                            Pembayaran
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-white/10">

                    @forelse($transaksi ?? [] as $index => $item)

                        <tr class="hover:bg-white/5 transition">

                            <td class="px-5 py-3 text-gray-400">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-5 py-3 text-white font-medium">
                                {{ $item->invoice_number }}
                            </td>

                            <td class="px-5 py-3 text-gray-400">
                                {{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}
                            </td>

                            <td class="px-5 py-3 text-gray-400">
                                {{ $item->user?->name ?? '-' }}
                            </td>

                            <td class="px-5 py-3 text-white font-medium">
                                Rp {{ number_format($item->total ?? 0, 0, ',', '.') }}
                            </td>

                            <td class="px-5 py-3 text-gray-400">
                                {{ ucfirst($item->payment_method ?? '-') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-5 py-10 text-center text-gray-500"
                            >
                                Belum ada transaksi pada periode ini.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>



    {{-- =========================================================
         RIWAYAT PENGELUARAN
    ========================================================== --}}

    <div class="bg-gray-800 rounded-xl border border-white/10 overflow-hidden">

        <div class="p-5 border-b border-white/10">

            <h2 class="text-lg font-semibold text-white">
                Riwayat Pengeluaran
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Daftar pengeluaran dalam periode laporan.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-gray-900 text-gray-400">

                    <tr>

                        <th class="px-5 py-3">
                            No
                        </th>

                        <th class="px-5 py-3">
                            Tanggal
                        </th>

                        <th class="px-5 py-3">
                            Kategori
                        </th>

                        <th class="px-5 py-3">
                            Keterangan
                        </th>

                        <th class="px-5 py-3">
                            Jumlah
                        </th>

                        <th class="px-5 py-3">
                            Dicatat Oleh
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-white/10">

                    @forelse($pengeluaran ?? [] as $index => $item)

                        <tr class="hover:bg-white/5 transition">

                            <td class="px-5 py-3 text-gray-400">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-5 py-3 text-gray-400">
                                {{ $item->expense_date?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td class="px-5 py-3 text-white">
                                {{ $item->category }}
                            </td>

                            <td class="px-5 py-3 text-gray-400">
                                {{ $item->description ?? '-' }}
                            </td>

                            <td class="px-5 py-3 text-white font-medium">
                                Rp {{ number_format($item->amount ?? 0, 0, ',', '.') }}
                            </td>

                            <td class="px-5 py-3 text-gray-400">
                                {{ $item->user?->name ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-5 py-10 text-center text-gray-500"
                            >
                                Belum ada pengeluaran pada periode ini.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


</div>

@endsection