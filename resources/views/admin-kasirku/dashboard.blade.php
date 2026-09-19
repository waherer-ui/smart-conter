@extends('layouts.app')

@section('title', 'Admin KasirKU')
@section('header', 'Admin KasirKU')

@section('content')

<div class="space-y-6">{{-- =========================================================
     HEADER
========================================================== --}}
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

    <div class="flex items-center gap-3">

        <div class="w-11 h-11 rounded-2xl
                    bg-emerald-500/15
                    border border-emerald-400/20
                    flex items-center justify-center
                    text-xl">
            🛡️
        </div>

        <div>
            <h1 class="text-2xl font-bold text-white">
                Dashboard Admin KasirKU
            </h1>

            <p class="text-sm text-gray-400 mt-1">
                Pusat kontrol dan monitoring platform KasirKU.
            </p>
        </div>

    </div>

    <div class="text-xs text-gray-500">
        Super Admin Platform
    </div>

</div>


{{-- =========================================================
     NAVIGASI ADMIN PLATFORM
========================================================== --}}
<div class="bg-gray-800/80 border border-white/10
            rounded-2xl p-3 shadow-xl">

    <div class="flex flex-wrap gap-2">

        {{-- DASHBOARD --}}
        <a
            href="{{ route('admin-kasirku.dashboard') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5
                   rounded-xl
                   bg-emerald-500/15
                   border border-emerald-400/20
                   text-emerald-400
                   text-sm font-semibold transition"
        >
            📊
            <span>Dashboard</span>
        </a>


        {{-- PENGGUNA --}}
        <a
            href="{{ route('admin-kasirku.users.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5
                   rounded-xl
                   bg-gray-700/70 hover:bg-gray-600
                   text-gray-200
                   text-sm font-medium transition"
        >
            👤
            <span>Pengguna</span>
        </a>


     {{-- TOKO --}}
     {{---   <a
            href="{{ route('admin-kasirku.stores.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5
                   rounded-xl
                   bg-gray-700/70 hover:bg-gray-600
                   text-gray-200
                   text-sm font-medium transition"
        >
            🏪
            <span>Toko</span>
        </a> --}}



        {{-- LANGGANAN --}}
        <a
            href="{{ route('admin-kasirku.subscriptions.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5
                   rounded-xl
                   bg-gray-700/70 hover:bg-gray-600
                   text-gray-200
                   text-sm font-medium transition"
        >
            💳
            <span>Langganan</span>
        </a>


        {{-- PAKET --}}
        <a
            href="{{ route('admin-kasirku.plans.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5
                   rounded-xl
                   bg-gray-700/70 hover:bg-gray-600
                   text-gray-200
                   text-sm font-medium transition"
        >
            📦
            <span>Paket</span>
        </a>


        {{-- PENGATURAN --}}
        <a
            href="{{ route('admin-kasirku.settings') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5
                   rounded-xl
                   bg-gray-700/70 hover:bg-gray-600
                   text-gray-200
                   text-sm font-medium transition"
        >
            ⚙️
            <span>Pengaturan</span>
        </a>

    </div>

</div>


{{-- =========================================================
     STATISTIK UTAMA
========================================================== --}}
<div>

    <div class="mb-3">

        <h2 class="text-base font-semibold text-white">
            Ringkasan Platform
        </h2>

        <p class="text-xs text-gray-500 mt-1">
            Statistik utama KasirKU saat ini.
        </p>

    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

        {{-- TOTAL USERS --}}
        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-5 shadow-xl">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Total Pengguna
                    </p>

                    <p class="text-3xl font-bold text-white mt-2">
                        {{ number_format($totalUsers, 0, ',', '.') }}
                    </p>

                    <p class="text-xs text-gray-500 mt-2">
                        Akun terdaftar di KasirKU
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl
                            bg-blue-500/10
                            flex items-center justify-center
                            text-xl">
                    👤
                </div>

            </div>

        </div>


        {{-- TOTAL STORES --}}
        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-5 shadow-xl">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Total Toko
                    </p>

                    <p class="text-3xl font-bold text-white mt-2">
                        {{ number_format($totalStores, 0, ',', '.') }}
                    </p>

                    <p class="text-xs text-gray-500 mt-2">
                        Toko terdaftar di platform
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl
                            bg-amber-500/10
                            flex items-center justify-center
                            text-xl">
                    🏪
                </div>

            </div>

        </div>


        {{-- ACTIVE SUBSCRIPTIONS --}}
        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-5 shadow-xl">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Langganan Aktif
                    </p>

                    <p class="text-3xl font-bold text-white mt-2">
                        {{ number_format($activeSubscriptions, 0, ',', '.') }}
                    </p>

                    <p class="text-xs text-gray-500 mt-2">
                        Subscription aktif
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl
                            bg-emerald-500/10
                            flex items-center justify-center
                            text-xl">
                    💳
                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     DISTRIBUSI PAKET
========================================================== --}}
<div>

    <div class="mb-3">

        <h2 class="text-base font-semibold text-white">
            Distribusi Paket
        </h2>

        <p class="text-xs text-gray-500 mt-1">
            Jumlah subscription aktif berdasarkan paket.
        </p>

    </div>


    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- FREE --}}
        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-5 shadow-xl">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Free
                    </p>

                    <p class="text-3xl font-bold text-white mt-2">
                        {{ number_format($freeSubscriptions, 0, ',', '.') }}
                    </p>

                    <p class="text-xs text-gray-500 mt-2">
                        Subscription aktif
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl
                            bg-gray-500/10
                            flex items-center justify-center
                            text-xl">
                    🆓
                </div>

            </div>

        </div>


        {{-- PRO --}}
        <div class="bg-gray-800/80 border border-emerald-400/20
                    rounded-2xl p-5 shadow-xl">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Pro
                    </p>

                    <p class="text-3xl font-bold text-emerald-400 mt-2">
                        {{ number_format($proSubscriptions, 0, ',', '.') }}
                    </p>

                    <p class="text-xs text-gray-500 mt-2">
                        Subscription aktif
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl
                            bg-emerald-500/10
                            flex items-center justify-center
                            text-xl">
                    💎
                </div>

            </div>

        </div>


        {{-- PREMIUM --}}
        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-5 shadow-xl">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-400">
                        Premium
                    </p>

                    <p class="text-3xl font-bold text-purple-400 mt-2">
                        {{ number_format($premiumSubscriptions, 0, ',', '.') }}
                    </p>

                    <p class="text-xs text-gray-500 mt-2">
                        Subscription aktif
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl
                            bg-purple-500/10
                            flex items-center justify-center
                            text-xl">
                    👑
                </div>

            </div>

        </div>

    </div>

</div>

{{-- =========================================================
     RINGKASAN BARU
========================================================== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mt-4">

    {{-- EST. PENDAPATAN / MRR --}}
    <div class="bg-gray-800/80 border border-white/10
                rounded-2xl p-5 shadow-xl">

        <div class="flex items-start justify-between">

            <div>

                <p class="text-sm text-gray-400">
                    Est. Pendapatan (MRR)
                </p>

                <p class="text-3xl font-bold text-emerald-400 mt-2">
                    Rp{{ number_format($estimatedMrr, 0, ',', '.') }}
                </p>

                <p class="text-xs text-gray-500 mt-2">
                    Total potensi pemasukan bulanan
                </p>

            </div>

            <div class="w-11 h-11 rounded-xl
                        bg-emerald-500/10
                        flex items-center justify-center
                        text-xl">
                💰
            </div>

        </div>

    </div>


    {{-- AKAN KADALUWARSA --}}
    <div class="bg-gray-800/80 border border-white/10
                rounded-2xl p-5 shadow-xl">

        <div class="flex items-start justify-between">

            <div>

                <p class="text-sm text-gray-400">
                    Akan Kadaluwarsa
                </p>

                <p class="text-3xl font-bold text-amber-400 mt-2">
                    {{ number_format($expiringSoon, 0, ',', '.') }}
                </p>

                <p class="text-xs text-gray-500 mt-2">
                    Perlu follow-up perpanjangan
                </p>

            </div>

            <div class="w-11 h-11 rounded-xl
                        bg-amber-500/10
                        flex items-center justify-center
                        text-xl">
                ⏳
            </div>

        </div>

    </div>


    {{-- TOTAL TRANSAKSI --}}
    <div class="bg-gray-800/80 border border-white/10
                rounded-2xl p-5 shadow-xl">

        <div class="flex items-start justify-between">

            <div>

                <p class="text-sm text-gray-400">
                    Total Transaksi
                </p>

                <p class="text-3xl font-bold text-indigo-400 mt-2">
                    {{ number_format($totalTransactions, 0, ',', '.') }}
                </p>

                <p class="text-xs text-gray-500 mt-2">
                    Volume penggunaan aplikasi
                </p>

            </div>

            <div class="w-11 h-11 rounded-xl
                        bg-indigo-500/10
                        flex items-center justify-center
                        text-xl">
                📈
            </div>

        </div>

    </div>

</div>

{{-- =========================================================
     AKTIVITAS TERBARU
========================================================== --}}
<div class="bg-gray-800/80 border border-white/10
            rounded-2xl p-5 shadow-xl">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row
                lg:items-center lg:justify-between
                gap-4 mb-5">

        <div>
            <h3 class="text-lg font-semibold text-white">
                Aktivitas Terbaru
            </h3>

            <p class="text-sm text-gray-400 mt-1">
                Perkembangan terbaru di platform KasirKU
            </p>
        </div>

        {{-- Filter --}}
        <div class="flex items-center gap-2
                    overflow-x-auto pb-1">

            <a
                href="{{ route('admin-kasirku.dashboard', ['activity' => 'all']) }}"
                class="whitespace-nowrap px-3 py-2 rounded-lg text-xs font-medium transition
                {{ $activity === 'all'
                    ? 'bg-emerald-500 text-white'
                    : 'bg-gray-700/60 text-gray-300 hover:bg-gray-700' }}"
            >
                Semua
            </a>

            <a
                href="{{ route('admin-kasirku.dashboard', ['activity' => 'owner']) }}"
                class="whitespace-nowrap px-3 py-2 rounded-lg text-xs font-medium transition
                {{ $activity === 'owner'
                    ? 'bg-emerald-500 text-white'
                    : 'bg-gray-700/60 text-gray-300 hover:bg-gray-700' }}"
            >
                👤 Owner
            </a>

            <a
                href="{{ route('admin-kasirku.dashboard', ['activity' => 'store']) }}"
                class="whitespace-nowrap px-3 py-2 rounded-lg text-xs font-medium transition
                {{ $activity === 'store'
                    ? 'bg-emerald-500 text-white'
                    : 'bg-gray-700/60 text-gray-300 hover:bg-gray-700' }}"
            >
                🏪 Toko
            </a>

            <a
                href="{{ route('admin-kasirku.dashboard', ['activity' => 'transaction']) }}"
                class="whitespace-nowrap px-3 py-2 rounded-lg text-xs font-medium transition
                {{ $activity === 'transaction'
                    ? 'bg-emerald-500 text-white'
                    : 'bg-gray-700/60 text-gray-300 hover:bg-gray-700' }}"
            >
                📈 Transaksi
            </a>

        </div>

    </div>


    {{-- =====================================================
         SEMUA
    ====================================================== --}}
   @if ($activity === 'all')

    <div class="space-y-1">

        @forelse ($activities as $item)

            <div class="flex gap-4 p-4
                        rounded-xl
                        hover:bg-white/[0.03]
                        transition">

                {{-- Icon --}}
                <div class="w-10 h-10 shrink-0
                            rounded-xl
                            bg-gray-700/60
                            flex items-center justify-center
                            text-lg">
                    {{ $item['icon'] }}
                </div>

                {{-- Content --}}
                <div class="min-w-0 flex-1">

                    <div class="flex flex-col sm:flex-row
                                sm:items-center
                                sm:justify-between
                                gap-1">

                        <p class="text-sm font-semibold text-white">
                            {{ $item['title'] }}
                        </p>

                        <span class="text-xs text-gray-500">
                            {{ $item['date']->diffForHumans() }}
                        </span>

                    </div>

                    <p class="text-sm text-gray-300 mt-1">
                        {{ $item['name'] }}
                    </p>

                    <p class="text-xs text-gray-500 mt-1">
                        {{ $item['description'] }}
                    </p>

                </div>

            </div>

        @empty

            <div class="text-center py-8">
                <p class="text-sm text-gray-500">
                    Belum ada aktivitas platform.
                </p>
            </div>

        @endforelse

    </div>


    {{-- =====================================================
         OWNER
    ====================================================== --}}
    @elseif ($activity === 'owner')

        <div class="space-y-3">

            @forelse ($recentOwners as $owner)

                <div class="flex items-center justify-between
                            gap-3 border border-white/10
                            rounded-xl p-4">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-full
                                    bg-emerald-500/10
                                    flex items-center justify-center
                                    text-emerald-400 font-bold">
                            {{ strtoupper(substr($owner->name, 0, 1)) }}
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-white">
                                {{ $owner->name }}
                            </p>

                            <p class="text-xs text-gray-500">
                                {{ $owner->email }}
                            </p>
                        </div>

                    </div>

                    <span class="text-xs text-gray-500">
                        {{ $owner->created_at->format('d M Y H:i') }}
                    </span>

                </div>

            @empty

                <p class="text-sm text-gray-500">
                    Belum ada aktivitas owner.
                </p>

            @endforelse

        </div>


    {{-- =====================================================
         TOKO
    ====================================================== --}}
    @elseif ($activity === 'store')

        <div class="space-y-3">

            @forelse ($recentStores as $store)

                <div class="flex items-center justify-between
                            gap-3 border border-white/10
                            rounded-xl p-4">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-xl
                                    bg-indigo-500/10
                                    flex items-center justify-center">
                            🏪
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-white">
                                {{ $store->name }}
                            </p>

                            <p class="text-xs text-gray-500">
                                Toko baru terdaftar
                            </p>
                        </div>

                    </div>

                    <span class="text-xs text-gray-500">
                        {{ $store->created_at->format('d M Y H:i') }}
                    </span>

                </div>

            @empty

                <p class="text-sm text-gray-500">
                    Belum ada aktivitas toko.
                </p>

            @endforelse

        </div>


    {{-- =====================================================
         TRANSAKSI
    ====================================================== --}}
    @elseif ($activity === 'transaction')

        <div class="space-y-3">

            @forelse ($recentTransactions as $transaction)

                <div class="flex items-center justify-between
                            gap-3 border border-white/10
                            rounded-xl p-4">

                    <div>
                        <p class="text-sm font-semibold text-white">
                            {{ $transaction->invoice_number }}
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ $transaction->created_at->format('d M Y H:i') }}
                        </p>
                    </div>

                    <span class="text-sm font-semibold text-emerald-400">
                        Rp{{ number_format($transaction->total, 0, ',', '.') }}
                    </span>

                </div>

            @empty

                <p class="text-sm text-gray-500">
                    Belum ada transaksi.
                </p>

            @endforelse

        </div>

    @endif

</div>


{{-- =========================================================
     INFORMASI PLATFORM
========================================================== --}}
<div>

    <div class="bg-gray-800/80 border border-white/10
                rounded-2xl p-5 shadow-xl">

        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl
                        bg-indigo-500/10
                        flex items-center justify-center">
                <span class="text-xl">ℹ️</span>
            </div>

            <div>
                <h3 class="font-semibold text-white">
                    Informasi Platform
                </h3>

                <p class="text-xs text-gray-400">
                    Informasi sistem KasirKU
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div>
                <p class="text-xs text-gray-400">
                    Status Platform
                </p>

                <p class="mt-1 text-sm font-semibold text-emerald-400">
                    ● Aktif
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-400">
                    Jenis Platform
                </p>

                <p class="mt-1 text-sm font-semibold text-white">
                    SaaS Kasir
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-400">
                    Model Bisnis
                </p>

                <p class="mt-1 text-sm font-semibold text-white">
                    Subscription
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-400">
                    Versi
                </p>

                <p class="mt-1 text-sm font-semibold text-white">
                    KasirKU
                </p>
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     FOOTER
========================================================== --}}
<div class="border-t border-white/10 pt-4">

    <div class="flex flex-col sm:flex-row
                items-center justify-between
                gap-2 text-xs text-gray-500">

        <p>
            © {{ date('Y') }} KasirKU.
            Semua hak dilindungi.
        </p>

        <p>
            Super Admin Platform
        </p>

    </div>

</div>

</div>
@endsection