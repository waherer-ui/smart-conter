@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('mobile_action', 'scan')
@section('header', ' 📊')
@section('header_tools')

    <div class="flex items-center gap-2 overflow-x-auto max-w-full">

        <a
            href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'laporan'])) }}"
            class="inline-flex items-center gap-1.5
                   px-3 py-2
                   rounded-xl
                   text-xs
                   font-semibold
                   whitespace-nowrap
                   transition
                   {{ ($tab ?? 'laporan') === 'laporan'
                        ? 'bg-emerald-500 text-gray-950'
                        : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}"
        >
            📊
            <span>Laporan</span>
        </a>

        <a
            href="{{ route('laporan', array_merge(request()->query(), ['tab' => 'analitik'])) }}"
            class="inline-flex items-center gap-1.5
                   px-3 py-2
                   rounded-xl
                   text-xs
                   font-semibold
                   whitespace-nowrap
                   transition
                   {{ ($tab ?? 'laporan') === 'analitik'
                        ? 'bg-emerald-500 text-gray-950'
                        : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}"
        >
            📈
            <span>Analitik Lanjutan</span>
        </a>

        <a
            href="{{ route('laporan.piutang') }}"
            class="inline-flex items-center gap-1.5
                   px-3 py-2
                   rounded-xl
                   text-xs
                   font-semibold
                   whitespace-nowrap
                   transition
                   bg-gray-800
                   hover:bg-gray-700
                   text-gray-300"
        >
            💰
            <span>Piutang</span>
        </a>

    </div>

@endsection

@section('content')

<div class="space-y-6">

    {{-- =========================================================
         CEK ROLE
    ========================================================== --}}

    @php
        $isAdmin = session('user_role') === 'admin';
    @endphp

    @if (($tab ?? 'laporan') === 'analitik')

    @if (!$store || !$store->hasFeature('advanced_analytics'))

        <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-6 text-center">
            <div class="text-4xl mb-3">📈</div>

            <h2 class="text-xl font-bold text-white mb-2">
                Analitik Lanjutan
            </h2>

            <p class="text-gray-400 text-sm max-w-md mx-auto mb-5">
                Dapatkan analisis lebih mendalam tentang penjualan,
                produk, keuntungan, waktu ramai, metode pembayaran,
                dan performa toko Anda.
            </p>

            <div class="inline-flex items-center gap-2
                        bg-amber-500/10
                        border border-amber-500/30
                        text-amber-400
                        px-4 py-2
                        rounded-xl
                        text-sm
                        font-semibold
                        mb-5">
                🔒 Fitur Premium
            </div>

            <div>
                <a
                    href="{{ route('paket') }}"
                    class="inline-flex items-center gap-2
                           bg-emerald-500
                           hover:bg-emerald-400
                           text-gray-950
                           px-5 py-2.5
                           rounded-xl
                           text-sm
                           font-bold
                           transition"
                >
                    🚀 Lihat Paket
                </a>
            </div>
        </div>

@else

    {{-- =========================================================
         ANALITIK LANJUTAN
    ========================================================== --}}

    <div class="space-y-5">

{{-- HEADER ANALITIK --}}
<div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5">

    <div class="flex flex-col gap-4">

        {{-- JUDUL --}}
        <div>
            <h2 class="text-xl font-bold text-white">
                📈 Analitik Lanjutan
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Analisis performa penjualan berdasarkan periode yang dipilih.
            </p>
        </div>

        {{-- FILTER PERIODE --}}
        <form
            method="GET"
            action="{{ route('laporan') }}"
            class="space-y-3"
        >

            <input type="hidden" name="tab" value="analitik">

            @if ($isAdmin && request('cashier_id'))
                <input
                    type="hidden"
                    name="cashier_id"
                    value="{{ request('cashier_id') }}"
                >
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">

                <div>
                    <label
                        for="analytics_period"
                        class="block text-xs text-gray-400 mb-1.5"
                    >
                        Periode
                    </label>

                    <select
                        id="analytics_period"
                        name="period"
                        onchange="toggleAnalyticsCustomDate()"
                        class="w-full bg-gray-900 border border-white/10
                               rounded-xl px-3 py-2.5 text-sm text-white
                               focus:outline-none focus:border-emerald-500"
                    >
                        <option value="today"
                            {{ ($period ?? 'today') === 'today' ? 'selected' : '' }}>
                            Hari ini
                        </option>

                        <option value="7days"
                            {{ ($period ?? '') === '7days' ? 'selected' : '' }}>
                            7 hari terakhir
                        </option>

                        <option value="1month"
                            {{ ($period ?? '') === '1month' ? 'selected' : '' }}>
                            1 bulan terakhir
                        </option>

                        <option value="3months"
                            {{ ($period ?? '') === '3months' ? 'selected' : '' }}>
                            3 bulan terakhir
                        </option>

                        <option value="6months"
                            {{ ($period ?? '') === '6months' ? 'selected' : '' }}>
                            6 bulan terakhir
                        </option>

                        <option value="1year"
                            {{ ($period ?? '') === '1year' ? 'selected' : '' }}>
                            1 tahun terakhir
                        </option>

                        <option value="custom"
                            {{ ($period ?? '') === 'custom' ? 'selected' : '' }}>
                            Rentang tanggal
                        </option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button
                        type="submit"
                        class="w-full bg-emerald-500 hover:bg-emerald-400
                               text-gray-950 font-bold rounded-xl
                               px-3 py-2.5 text-sm transition"
                    >
                        🔎 Terapkan
                    </button>
                </div>

            </div>

            {{-- TANGGAL CUSTOM --}}
            <div
                id="analytics-custom-date"
                class="{{ ($period ?? 'today') === 'custom' ? '' : 'hidden' }}"
            >

                <div class="grid grid-cols-2 gap-2">

                    <div>
                        <label
                            for="analytics_start_date"
                            class="block text-xs text-gray-400 mb-1.5"
                        >
                            Dari tanggal
                        </label>

                        <input
                            type="date"
                            id="analytics_start_date"
                            name="start_date"
                            value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                            class="w-full bg-gray-900 border border-white/10
                                   rounded-xl px-3 py-2.5 text-sm text-white
                                   focus:outline-none focus:border-emerald-500"
                        >
                    </div>

                    <div>
                        <label
                            for="analytics_end_date"
                            class="block text-xs text-gray-400 mb-1.5"
                        >
                            Sampai tanggal
                        </label>

                        <input
                            type="date"
                            id="analytics_end_date"
                            name="end_date"
                            value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                            class="w-full bg-gray-900 border border-white/10
                                   rounded-xl px-3 py-2.5 text-sm text-white
                                   focus:outline-none focus:border-emerald-500"
                        >
                    </div>

                </div>

            </div>

        </form>

        {{-- PERIODE AKTIF --}}
        <div class="flex items-center justify-between gap-3
                    bg-gray-900/70 border border-white/5
                    rounded-xl px-4 py-3">

            <div>
                <p class="text-xs text-gray-500">
                    Periode analitik
                </p>

                <p class="text-sm font-semibold text-white mt-0.5">
                    {{ $startDate->format('d/m/Y') }}
                    -
                    {{ $endDate->format('d/m/Y') }}
                </p>
            </div>

            <div class="text-emerald-400 text-lg">
                📅
            </div>

        </div>

    </div>

</div>

<script>
function toggleAnalyticsCustomDate() {
    const period = document.getElementById('analytics_period').value;
    const customDate = document.getElementById('analytics-custom-date');

    if (period === 'custom') {
        customDate.classList.remove('hidden');
    } else {
        customDate.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    toggleAnalyticsCustomDate();
});
</script>


        {{-- =====================================================
             RINGKASAN ANALITIK
        ====================================================== --}}

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">

            {{-- Omzet --}}
            <div class="bg-gray-800 rounded-xl border border-white/10 p-4">

                <p class="text-xs text-gray-400">
                    Total Omzet
                </p>

                <p class="text-lg sm:text-xl font-bold text-white mt-1">
                    Rp {{ number_format($omzet ?? 0, 0, ',', '.') }}
                </p>

            </div>


            {{-- Transaksi --}}
            <div class="bg-gray-800 rounded-xl border border-white/10 p-4">

                <p class="text-xs text-gray-400">
                    Total Transaksi
                </p>

                <p class="text-lg sm:text-xl font-bold text-white mt-1">
                    {{ $totalTransaksi ?? 0 }}
                </p>

            </div>


            {{-- Rata-rata --}}
            <div class="bg-gray-800 rounded-xl border border-white/10 p-4">

                <p class="text-xs text-gray-400">
                    Rata-rata Transaksi
                </p>

                <p class="text-lg sm:text-xl font-bold text-emerald-400 mt-1">
                    Rp {{ number_format($analitik['rataRataTransaksi'] ?? 0, 0, ',', '.') }}
                </p>

            </div>


            {{-- Barang --}}
            <div class="bg-gray-800 rounded-xl border border-white/10 p-4">

                <p class="text-xs text-gray-400">
                    Barang Terjual
                </p>

                <p class="text-lg sm:text-xl font-bold text-white mt-1">
                    {{ $barangTerjual ?? 0 }} Pcs
                </p>

            </div>

        </div>


        {{-- =====================================================
             TREN PENJUALAN
        ====================================================== --}}

        <div class="bg-gray-800 rounded-xl border border-white/10 p-5">

    <div class="mb-4">
        <h3 class="text-base font-semibold text-white">
            📈 Grafik Tren Omzet
        </h3>

        <p class="text-xs text-gray-400 mt-1">
            Pergerakan omzet penjualan berdasarkan tanggal.
        </p>
    </div>

    <div class="relative h-64 sm:h-72">
        <canvas id="trenOmzetChart"></canvas>
    </div>

</div>


        <div class="bg-gray-800 rounded-xl border border-white/10 overflow-hidden">

            <div class="p-5 border-b border-white/10">

                <h3 class="text-base font-semibold text-white">
                    📊 Tren Penjualan
                </h3>

                <p class="text-xs text-gray-400 mt-1">
                    Performa omzet dan transaksi setiap hari.
                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm text-left">

                    <thead class="bg-gray-900 text-gray-400">

                        <tr>

                            <th class="px-5 py-3">
                                Tanggal
                            </th>

                            <th class="px-5 py-3">
                                Transaksi
                            </th>

                            <th class="px-5 py-3">
                                Omzet
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-white/10">

                        @forelse(($analitik['trenPenjualan'] ?? []) as $item)

                            <tr class="hover:bg-white/5">

                                <td class="px-5 py-3 text-white">
                                    {{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}
                                </td>

                                <td class="px-5 py-3 text-gray-400">
                                    {{ $item['transaksi'] }} transaksi
                                </td>

                                <td class="px-5 py-3 text-white font-medium">
                                    Rp {{ number_format($item['omzet'], 0, ',', '.') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="3"
                                    class="px-5 py-8 text-center text-gray-500"
                                >
                                    Belum ada data penjualan pada periode ini.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

{{-- =====================================================
     CHART PRODUK TERLARIS
====================================================== --}}

<div class="bg-gray-800 rounded-xl border border-white/10 p-5 mb-5">

    <div class="mb-4">
        <h3 class="text-base font-semibold text-white">
            🏆 Grafik Produk Terlaris
        </h3>

        <p class="text-xs text-gray-400 mt-1">
            Produk berdasarkan jumlah unit yang terjual.
        </p>
    </div>

    <div class="relative h-72">
        <canvas id="produkTerlarisChart"></canvas>
    </div>

</div>


{{-- =====================================================
     PRODUK TERLARIS + PRODUK MENGUNTUNGKAN
====================================================== --}}

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">


            {{-- PRODUK TERLARIS --}}
            <div class="bg-gray-800 rounded-xl border border-white/10 overflow-hidden">

                <div class="p-5 border-b border-white/10">

                    <h3 class="text-base font-semibold text-white">
                        🏆 Produk Terlaris
                    </h3>

                    <p class="text-xs text-gray-400 mt-1">
                        Produk dengan jumlah penjualan terbanyak.
                    </p>

                </div>


                <div class="divide-y divide-white/10">

                    @forelse(($analitik['produkTerlaris'] ?? []) as $index => $item)

                        <div class="p-4 flex items-center gap-3">

                            <div class="w-8 h-8 rounded-lg
                                        bg-gray-900
                                        flex items-center justify-center
                                        text-xs font-bold text-emerald-400">
                                {{ $index + 1 }}
                            </div>

                            <div class="flex-1 min-w-0">

                                <p class="text-sm font-semibold text-white truncate">
                                    {{ $item['nama'] }}
                                </p>

                                <p class="text-xs text-gray-500 mt-0.5">
                                    Rp {{ number_format($item['omzet'], 0, ',', '.') }}
                                </p>

                            </div>

                            <div class="text-right">

                                <p class="text-sm font-bold text-white">
                                    {{ $item['terjual'] }}
                                </p>

                                <p class="text-xs text-gray-500">
                                    terjual
                                </p>

                            </div>

                        </div>

                    @empty

                        <div class="p-8 text-center text-gray-500 text-sm">
                            Belum ada data produk.
                        </div>

                    @endforelse

                </div>

            </div>


            {{-- PRODUK PALING MENGUNTUNGKAN --}}
            @if($isAdmin)

                <div class="bg-gray-800 rounded-xl border border-white/10 overflow-hidden">

                    <div class="p-5 border-b border-white/10">

                        <h3 class="text-base font-semibold text-white">
                            💰 Produk Paling Menguntungkan
                        </h3>

                        <p class="text-xs text-gray-400 mt-1">
                            Produk dengan laba kotor terbesar.
                        </p>

                    </div>


                    <div class="divide-y divide-white/10">

                        @forelse(($analitik['produkMenguntungkan'] ?? []) as $index => $item)

                            <div class="p-4 flex items-center gap-3">

                                <div class="w-8 h-8 rounded-lg
                                            bg-gray-900
                                            flex items-center justify-center
                                            text-xs font-bold text-emerald-400">
                                    {{ $index + 1 }}
                                </div>

                                <div class="flex-1 min-w-0">

                                    <p class="text-sm font-semibold text-white truncate">
                                        {{ $item['nama'] }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-0.5">
                                        HPP Rp {{ number_format($item['hpp'], 0, ',', '.') }}
                                    </p>

                                </div>

                                <div class="text-right">

                                    <p class="text-sm font-bold
                                        {{ $item['laba'] >= 0
                                            ? 'text-green-400'
                                            : 'text-red-400' }}">

                                        Rp {{ number_format($item['laba'], 0, ',', '.') }}

                                    </p>

                                    <p class="text-xs text-gray-500">
                                        laba
                                    </p>

                                </div>

                            </div>

                        @empty

                            <div class="p-8 text-center text-gray-500 text-sm">
                                Belum ada data laba produk.
                            </div>

                        @endforelse

                    </div>

                </div>

            @endif

        </div>

{{-- =====================================================
     CHART METODE PEMBAYARAN
====================================================== --}}

<div class="bg-gray-800 rounded-xl border border-white/10 p-5 mb-5">

    <div class="mb-4">
        <h3 class="text-base font-semibold text-white">
            💳 Grafik Metode Pembayaran
        </h3>

        <p class="text-xs text-gray-400 mt-1">
            Perbandingan omzet berdasarkan metode pembayaran.
        </p>
    </div>

    <div class="relative h-72 flex justify-center">
        <canvas id="metodePembayaranChart"></canvas>
    </div>

</div>


{{-- =====================================================
     METODE PEMBAYARAN + JAM RAMAI
====================================================== --}}

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">


            {{-- METODE PEMBAYARAN --}}
            <div class="bg-gray-800 rounded-xl border border-white/10 overflow-hidden">

                <div class="p-5 border-b border-white/10">

                    <h3 class="text-base font-semibold text-white">
                        💳 Metode Pembayaran
                    </h3>

                    <p class="text-xs text-gray-400 mt-1">
                        Distribusi transaksi berdasarkan metode pembayaran.
                    </p>

                </div>


                <div class="divide-y divide-white/10">

                    @forelse(($analitik['metodePembayaran'] ?? []) as $item)

                        <div class="p-4 flex items-center justify-between gap-4">

                            <div>

                                <p class="text-sm font-semibold text-white">
                                    {{ $item['metode'] }}
                                </p>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $item['transaksi'] }} transaksi
                                </p>

                            </div>

                            <p class="text-sm font-bold text-white">
                                Rp {{ number_format($item['total'], 0, ',', '.') }}
                            </p>

                        </div>

                    @empty

                        <div class="p-8 text-center text-gray-500 text-sm">
                            Belum ada data pembayaran.
                        </div>

                    @endforelse

                </div>

            </div>


            {{-- JAM RAMAI --}}
            <div class="bg-gray-800 rounded-xl border border-white/10 overflow-hidden">

                <div class="p-5 border-b border-white/10">

                    <h3 class="text-base font-semibold text-white">
                        ⏰ Jam Ramai
                    </h3>

                    <p class="text-xs text-gray-400 mt-1">
                        Waktu dengan jumlah transaksi terbanyak.
                    </p>

                </div>


                <div class="divide-y divide-white/10">

                    @forelse(($analitik['jamRamai'] ?? []) as $item)

                        <div class="p-4 flex items-center justify-between gap-4">

                            <div>

                                <p class="text-sm font-semibold text-white">
                                    {{ $item['jam'] }}
                                </p>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $item['transaksi'] }} transaksi
                                </p>

                            </div>

                            <p class="text-sm font-bold text-white">
                                Rp {{ number_format($item['omzet'], 0, ',', '.') }}
                            </p>

                        </div>

                    @empty

                        <div class="p-8 text-center text-gray-500 text-sm">
                            Belum ada data jam transaksi.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

@endif

@else


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

    <div class="mb-3">

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
        class="space-y-3"
    >

        {{-- TAB --}}
        <input
            type="hidden"
            name="tab"
            value="laporan"
        >


        <div class="grid grid-cols-1
                    {{ $isAdmin ? 'md:grid-cols-2' : 'md:grid-cols-1' }}
                    gap-2 sm:gap-4">


            {{-- =================================================
                 FILTER KASIR
                 HANYA ADMIN
            ================================================== --}}

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
                               rounded-lg px-3 py-2.5 text-white
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


            {{-- =================================================
                 PERIODE
            ================================================== --}}

            <div>

                <label
                    for="report_period"
                    class="block text-sm text-gray-400 mb-2"
                >
                    Periode
                </label>

                <select
                    id="report_period"
                    name="period"
                    onchange="toggleReportCustomDate()"
                    class="w-full bg-gray-900 border border-white/10
                           rounded-lg px-3 py-2.5 text-white
                           focus:outline-none focus:border-blue-500"
                >

                    <option value="today"
                        {{ ($period ?? 'today') === 'today' ? 'selected' : '' }}>
                        Hari ini
                    </option>

                    <option value="7days"
                        {{ ($period ?? '') === '7days' ? 'selected' : '' }}>
                        7 hari terakhir
                    </option>

                    <option value="1month"
                        {{ ($period ?? '') === '1month' ? 'selected' : '' }}>
                        1 bulan terakhir
                    </option>

                    <option value="3months"
                        {{ ($period ?? '') === '3months' ? 'selected' : '' }}>
                        3 bulan terakhir
                    </option>

                    <option value="6months"
                        {{ ($period ?? '') === '6months' ? 'selected' : '' }}>
                        6 bulan terakhir
                    </option>

                    <option value="1year"
                        {{ ($period ?? '') === '1year' ? 'selected' : '' }}>
                        1 tahun terakhir
                    </option>

                    <option value="custom"
                        {{ ($period ?? '') === 'custom' ? 'selected' : '' }}>
                        Rentang tanggal
                    </option>

                </select>

            </div>

        </div>


        {{-- =================================================
             RENTANG TANGGAL CUSTOM
        ================================================== --}}

        <div
            id="report-custom-date"
            class="{{ ($period ?? 'today') === 'custom' ? '' : 'hidden' }}"
        >

            <div class="grid grid-cols-2 gap-2 sm:gap-4">

                <div>

                    <label
                        for="report_start_date"
                        class="block text-sm text-gray-400 mb-2"
                    >
                        Dari Tanggal
                    </label>

                    <input
                        type="date"
                        id="report_start_date"
                        name="start_date"
                        value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                        class="w-full bg-gray-900 border border-white/10
                               rounded-lg px-3 py-2.5 text-white
                               focus:outline-none focus:border-blue-500"
                    >

                </div>


                <div>

                    <label
                        for="report_end_date"
                        class="block text-sm text-gray-400 mb-2"
                    >
                        Sampai Tanggal
                    </label>

                    <input
                        type="date"
                        id="report_end_date"
                        name="end_date"
                        value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                        class="w-full bg-gray-900 border border-white/10
                               rounded-lg px-3 py-2.5 text-white
                               focus:outline-none focus:border-blue-500"
                    >

                </div>

            </div>

        </div>


        {{-- =================================================
             TOMBOL
        ================================================== --}}

        <div class="flex items-center gap-2">

            <button
                type="submit"
                class="flex-1 bg-blue-600 hover:bg-blue-700
                       text-white font-medium rounded-lg
                       px-3 sm:px-4 py-2.5 transition
                       text-xs sm:text-sm"
            >
                🔎 Cari
            </button>

            <a
                href="{{ route('laporan') }}"
                class="bg-gray-700 hover:bg-gray-600
                       text-white font-medium rounded-lg
                       px-4 py-2.5 transition
                       text-xs sm:text-sm"
            >
                Reset
            </a>

        </div>

    </form>

</div>


<script>
function toggleReportCustomDate() {

    const period = document.getElementById('report_period');
    const customDate = document.getElementById('report-custom-date');

    if (!period || !customDate) {
        return;
    }

    if (period.value === 'custom') {
        customDate.classList.remove('hidden');
    } else {
        customDate.classList.add('hidden');
    }
}


document.addEventListener('DOMContentLoaded', function () {
    toggleReportCustomDate();
});
</script>



    {{-- =========================================================
         RINGKASAN PERIODE
    ========================================================== --}}
    @if($historyRestricted)

<div class="bg-gray-800 rounded-xl border border-white/10 p-10 text-center">

    <div class="text-4xl mb-3">
        🔒
    </div>

    <h3 class="text-sm font-semibold text-white">
        Laporan di Luar Batas Paket
    </h3>

    <p class="text-xs text-gray-400 mt-2">
        Paket Anda hanya dapat mengakses
        laporan {{ $historyDays }} hari terakhir.
    </p>

    <p class="text-xs text-gray-500 mt-2">
        Data transaksi dan pengeluaran lama tetap tersimpan
        dan tidak dihapus.
    </p>

    <a
        href="{{ route('paket') }}"
        class="inline-block mt-5
               bg-emerald-500
               hover:bg-emerald-400
               text-gray-950
               px-5 py-2.5
               rounded-xl
               text-xs
               font-semibold
               transition"
    >
        🚀 Upgrade Paket
    </a>

</div>

@else

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
  @endif
  @endif

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
     * =====================================================
     * DATA DARI LARAVEL
     * =====================================================
     */

    const trenPenjualan = @json($analitik['trenPenjualan'] ?? []);
    const produkTerlaris = @json($analitik['produkTerlaris'] ?? []);
    const metodePembayaran = @json($analitik['metodePembayaran'] ?? []);


    /*
     * =====================================================
     * GRAFIK TREN OMZET
     * =====================================================
     */

    const trenCanvas = document.getElementById('trenOmzetChart');

    if (trenCanvas && trenPenjualan.length > 0) {

        new Chart(trenCanvas, {
            type: 'line',

            data: {
                labels: trenPenjualan.map(item => {
                    const date = new Date(item.tanggal + 'T00:00:00');

                    return date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short'
                    });
                }),

                datasets: [{
                    label: 'Omzet',

                    data: trenPenjualan.map(item =>
                        Number(item.omzet || 0)
                    ),

                    tension: 0.35,

                    fill: true,

                    borderWidth: 2,

                    pointRadius: 4,

                    pointHoverRadius: 6
                }]
            },

            options: {
                responsive: true,

                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Rp ' +
                                    new Intl.NumberFormat('id-ID').format(
                                        context.raw
                                    );
                            }
                        }
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true,

                        ticks: {
                            callback: function (value) {
                                return 'Rp ' +
                                    new Intl.NumberFormat('id-ID', {
                                        notation: 'compact',
                                        maximumFractionDigits: 1
                                    }).format(value);
                            }
                        }
                    }
                }
            }
        });
    }


    /*
     * =====================================================
     * GRAFIK PRODUK TERLARIS
     * =====================================================
     */

    const produkCanvas = document.getElementById('produkTerlarisChart');

    if (produkCanvas && produkTerlaris.length > 0) {

        new Chart(produkCanvas, {
            type: 'bar',

            data: {
                labels: produkTerlaris.map(item =>
                    item.nama
                ),

                datasets: [{
                    label: 'Terjual',

                    data: produkTerlaris.map(item =>
                        Number(item.terjual || 0)
                    ),

                    borderWidth: 1,

                    borderRadius: 6
                }]
            },

            options: {
                responsive: true,

                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.raw + ' pcs terjual';
                            }
                        }
                    }
                },

                scales: {
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },

                    y: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }


    /*
     * =====================================================
     * GRAFIK METODE PEMBAYARAN
     * =====================================================
     */

    const pembayaranCanvas =
        document.getElementById('metodePembayaranChart');

    if (pembayaranCanvas && metodePembayaran.length > 0) {

        new Chart(pembayaranCanvas, {
            type: 'doughnut',

            data: {
                labels: metodePembayaran.map(item =>
                    item.metode
                ),

                datasets: [{
                    label: 'Omzet',

                    data: metodePembayaran.map(item =>
                        Number(item.total || 0)
                    ),

                    borderWidth: 2
                }]
            },

            options: {
                responsive: true,

                maintainAspectRatio: false,

                cutout: '62%',

                plugins: {
                    legend: {
                        position: 'bottom',

                        labels: {
                            padding: 16
                        }
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {

                                return context.label + ': Rp ' +
                                    new Intl.NumberFormat('id-ID').format(
                                        context.raw
                                    );

                            }
                        }
                    }
                }
            }
        });
    }

});
</script>

@endsection