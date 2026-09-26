<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-900">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    
    <link rel="manifest" href="{{ asset('manifest.json') }}">

<meta name="theme-color" content="#059669">

<meta name="mobile-web-app-capable" content="yes">

<meta name="apple-mobile-web-app-capable" content="yes">

<meta
    name="apple-mobile-web-app-status-bar-style"
    content="black-translucent"
>

<meta
    name="apple-mobile-web-app-title"
    content="KasirKU"
>

    <title>
        KasirKU - @yield('title', 'Dashboard')
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>


<body class="min-h-screen bg-gray-900 text-gray-200 flex flex-col">


{{-- ========================================================= --}}
{{-- KASIRKU SPLASH SCREEN --}}
{{-- ========================================================= --}}

@if(session('logged_in'))

    <div
        id="kasirku-splash"
        class="fixed inset-0 z-[9999]
               hidden items-center justify-center
               bg-gray-900
               opacity-0
               transition-opacity duration-500"
    >

        <div class="flex flex-col items-center text-center">

            <div
                class="flex h-20 w-20
                       items-center justify-center
                       rounded-3xl
                       bg-emerald-500
                       text-4xl
                       font-black
                       text-white
                       shadow-2xl
                       shadow-emerald-500/30"
            >
                K
            </div>

            <div
                class="mt-5
                       text-3xl
                       font-black
                       tracking-tight"
            >
                <span class="text-emerald-400">
                    asir
                </span>

                <span class="text-white">
                    KU
                </span>
            </div>

            <p
                class="mt-2
                       text-sm
                       text-gray-400"
            >
                Solusi kasir untuk usaha Anda.
            </p>

        </div>

    </div>

@endif

{{-- ========================================================= --}}
{{-- DATA USER & TOKO AKTIF --}}
{{-- ========================================================= --}}

@php

$layoutUser = session('logged_in')
    ? \App\Models\User::find(session('user_id'))
    : null;

$avatarUrl = ($layoutUser && $layoutUser->avatar)
    ? \Illuminate\Support\Facades\Storage::disk('s3')->url($layoutUser->avatar)
    : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100';

$layoutStores = $layoutUser
    ? $layoutUser->stores()
        ->orderBy('stores.id')
        ->get()
    : collect();

$activeStoreId = session('active_store_id');

$activeStore = $layoutStores->firstWhere(
    'id',
    $activeStoreId
);

$supportWaitingCount = 0;

if ($layoutUser) {
    $supportWaitingCount = \App\Models\SupportTicket::where(
        'owner_id',
        $layoutUser->id
    )
    ->where('status', 'waiting')
    ->count();
}

@endphp



{{-- ========================================================= --}}
{{-- NAVBAR GLOBAL --}}
{{-- ========================================================= --}}

<nav class="bg-gray-800 border-b border-white/10 sticky top-0 z-50">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex h-16 items-center justify-between">


            {{-- ================================================= --}}
            {{-- LOGO --}}
            {{-- ================================================= --}}

            <div class="flex items-center">

                <a
                    href="{{ session('logged_in')
                        ? route('dashboard')
                        : route('login') }}"
                    class="flex items-center gap-1"
                >

                    <div
                        class="flex h-9 w-9 items-center justify-center
                               rounded-xl bg-emerald-500
                               text-lg font-black text-white
                               shadow-lg shadow-emerald-500/20"
                    >
                        K
                    </div>

                    <span class="text-xl font-black tracking-tight">

                        <span class="text-emerald-400">
                            asir
                        </span>

                        <span class="text-white">
                            KU
                        </span>

                    </span>

                </a>

            </div>



            {{-- ================================================= --}}
            {{-- DESKTOP --}}
            {{-- ================================================= --}}

            <div class="hidden md:flex items-center">

                <div class="relative">


                    {{-- ========================================= --}}
                    {{-- USER LOGIN --}}
                    {{-- ========================================= --}}

                    @if(session('logged_in'))

                        <div class="flex items-center gap-2">
                          
                          {{-- ================================= --}}
{{-- QUICK HELP --}}
{{-- ================================= --}}

<a
    href="{{ route('support.index') }}"
    class="relative
           flex items-center justify-center
           w-10 h-10
           rounded-xl
           text-gray-300
           hover:bg-gray-700
           hover:text-white
           transition
           focus:outline-none
           focus:ring-2
           focus:ring-indigo-500"
    aria-label="Pusat Bantuan"
    title="Pusat Bantuan"
>
    <span class="text-lg">
        💬
    </span>

    @if($supportWaitingCount > 0)
        <span
            class="absolute
                   top-1 right-1
                   min-w-[15px] h-[15px]
                   px-1
                   rounded-full
                   bg-amber-500
                   text-[9px]
                   font-bold
                   text-gray-900
                   flex items-center justify-center
                   border-2 border-gray-800"
        >
            {{ $supportWaitingCount > 9 ? '9+' : $supportWaitingCount }}
        </span>
    @endif
</a>


{{-- ================================= --}}
{{-- STORE SWITCHER / PLATFORM ACCESS --}}
{{-- ================================= --}}

@if(session('is_platform_admin'))

    {{-- ========================================= --}}
    {{-- SUPER ADMIN --}}
    {{-- ========================================= --}}

    <div class="hidden md:block text-right">

        <div
            class="text-sm font-semibold
                   text-emerald-400"
        >
            🛡️ Super Admin
        </div>

        <div class="text-xs text-gray-400">
            Platform KasirKU
        </div>

    </div>

@elseif($activeStore)

    {{-- ========================================= --}}
    {{-- OWNER / ADMIN TOKO --}}
    {{-- ========================================= --}}

    @if(session('user_role') === 'admin')

        <div class="relative">

            <button
                type="button"
                onclick="toggleStoreDropdown(event)"
                class="text-right rounded-xl px-3 py-2
                       hover:bg-gray-700 transition
                       focus:outline-none
                       focus:ring-2
                       focus:ring-emerald-500"
            >

                <div
                    class="text-sm font-semibold
                           text-emerald-400
                           truncate max-w-[180px]"
                >
                    🏪 {{ $activeStore->name }} ▾
                </div>

                <div class="text-xs text-gray-400">
                    Toko aktif
                </div>

            </button>


            <div
                id="desktop-store-dropdown"
                class="hidden absolute right-0 mt-2
                       w-56 rounded-2xl
                       bg-gray-800
                       border border-white/10
                       shadow-2xl
                       overflow-hidden z-50"
            >

                @php
                    $storeLimit = $activeStore?->getLimit('max_stores');
                @endphp


                @foreach($layoutStores as $index => $store)

                    @php
                        $storeLocked =
                            $storeLimit !== null &&
                            $index >= $storeLimit;
                    @endphp


                    @if($storeLocked)

                        {{-- TOKO TERKUNCI --}}

                        <div
                            class="dropdown-link
                                   cursor-not-allowed
                                   opacity-60"
                        >

                            <span>
                                🏪
                            </span>

                            <span
                                class="flex-1
                                       text-left
                                       truncate"
                            >
                                {{ $store->name }}
                            </span>

                            <span
                                class="text-xs
                                       text-amber-400
                                       font-semibold"
                            >
                                🔒
                            </span>

                        </div>

                    @else

                        {{-- TOKO BISA DIAKSES --}}

                        <form
                            action="{{ route('store.switch', $store->id) }}"
                            method="POST"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="dropdown-link"
                            >

                                <span>
                                    🏪
                                </span>

                                <span
                                    class="flex-1
                                           text-left
                                           truncate"
                                >
                                    {{ $store->name }}
                                </span>


                                @if($store->id == $activeStoreId)

                                    <span
                                        class="text-emerald-400
                                               font-bold"
                                    >
                                        ✓
                                    </span>

                                @endif

                            </button>

                        </form>

                    @endif

                @endforeach


                <a
                    href="{{ route('store.create') }}"
                    class="dropdown-link
                           border-t
                           border-white/10"
                >

                    <span>
                        ➕
                    </span>

                    <span class="flex-1 text-left">
                        Tambah Toko/Cabang
                    </span>

                </a>

            </div>

        </div>

    @else

        {{-- ================================= --}}
        {{-- KASIR --}}
        {{-- ================================= --}}

        <div class="text-right">

            <div
                class="text-sm font-semibold
                       text-emerald-400
                       truncate max-w-[180px]"
            >
                🏪 {{ $activeStore->name }}
            </div>

            <div class="text-xs text-gray-400">
                Toko aktif
            </div>

        </div>

    @endif

@endif



                            {{-- ================================= --}}
                            {{-- PROFILE BUTTON --}}
                            {{-- ================================= --}}

                            <button
                                onclick="toggleUserDropdown()"
                                type="button"
                                class="flex items-center gap-3
                                       rounded-xl px-3 py-2
                                       hover:bg-gray-700 transition
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-indigo-500"
                            >

                                <div class="text-right">

                                    <div
                                        class="text-sm font-semibold
                                               text-white"
                                    >
                                        {{ session('username') }}
                                    </div>

                                    <div class="text-xs text-gray-400">
                                        {{ session('user_role') }}
                                    </div>

                                </div>

                                <img
                                    class="w-9 h-9 rounded-full
                                           border border-white/20
                                           object-cover"
                                    src="{{ $avatarUrl }}"
                                    alt="Profile"
                                >

                                <svg
                                    class="w-4 h-4 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />

                                </svg>

                            </button>

                        </div>


                    @else

                        {{-- GUEST --}}

                        <button
                            onclick="toggleUserDropdown()"
                            type="button"
                            class="flex items-center justify-center
                                   w-11 h-11 rounded-xl
                                   hover:bg-gray-700 transition
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-indigo-500"
                            aria-label="Menu"
                        >

                            <svg
                                class="w-6 h-6 text-gray-300"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />

                            </svg>

                        </button>

                    @endif



                    {{-- ========================================= --}}
                    {{-- DESKTOP PROFILE DROPDOWN --}}
                    {{-- ========================================= --}}

                    <div
                        id="user-dropdown"
                        class="hidden absolute right-0 mt-2 w-64
                               rounded-2xl bg-gray-800
                               border border-white/10
                               shadow-2xl overflow-hidden z-50"
                    >

                        @if(session('logged_in'))

                            <div class="px-4 py-4 bg-gray-800/80">

                                <div class="flex items-center gap-3">

                                    <img
                                        class="w-11 h-11 rounded-full
                                               border border-white/20
                                               object-cover"
                                        src="{{ $avatarUrl }}"
                                        alt="Profile"
                                    >

                                    <div class="min-w-0">

                                        <div
                                            class="font-semibold text-white
                                                   truncate"
                                        >
                                            {{ session('username') }}
                                        </div>

                                        <div
                                            class="text-xs text-gray-400
                                                   uppercase"
                                        >
                                            {{ session('user_role') }}
                                        </div>

                                    </div>

                                </div>
                                {{-- ========================================= --}}
                            {{-- INFORMASI PAKET --}}
                            {{-- ========================================= --}}
                            
                            <div class="px-4 pb-4">
                            
                                 <x-plan-info :active-store="$activeStore" />
                            
                            </div>

                            </div>

                        @endif


{{-- NAVIGASI DESKTOP --}}

@if(session('is_platform_admin'))

    {{-- ==============================
         SUPER ADMIN KASIRKU
    =============================== --}}

    <div class="border-t border-white/10 py-2">

        <div
            class="px-4 py-1.5 text-xs
                   font-semibold text-gray-500
                   uppercase"
        >
            Platform
        </div>

        <a
            href="{{ route('admin-kasirku.dashboard') }}"
            class="dropdown-link"
        >
            <span>🏠</span>
            <span>Dashboard</span>
        </a>

        <a
            href="{{ route('admin-kasirku.users.index') }}"
            class="dropdown-link"
        >
            <span>👥</span>
            <span>Pengguna</span>
        </a>

        <a
            href="{{ route('admin-kasirku.stores.index') }}"
            class="dropdown-link"
        >
            <span>🏪</span>
            <span>Toko</span>
        </a>

        <a
            href="{{ route('admin-kasirku.subscriptions.index') }}"
            class="dropdown-link"
        >
            <span>💳</span>
            <span>Langganan</span>
        </a>

        <a
            href="{{ route('admin-kasirku.plans.index') }}"
            class="dropdown-link"
        >
            <span>📦</span>
            <span>Paket</span>
        </a>
        
        <a
              href="{{ route('admin-kasirku.audit-log.index') }}"
              class="dropdown-link
                     {{ request()->routeIs('admin-kasirku.audit-log.*')
                          ? 'bg-gray-700 text-white'
                          : '' }}"
          >
              <span>📋</span>
              <span>Semua Aktivitas</span>
          </a>

    </div>


    {{-- OPERASIONAL --}}

    <div class="border-t border-white/10 py-2">

        <div
            class="px-4 py-1.5 text-xs
                   font-semibold text-gray-500
                   uppercase"
        >
            Operasional
        </div>

        <a
            href="{{ route('admin-kasirku.support.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('admin-kasirku.support.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span class="relative">
                💬

                @if($supportWaitingCount > 0)
                    <span
                        class="absolute -top-1 -right-2
                               min-w-[16px] h-4
                               px-1
                               rounded-full
                               bg-amber-500
                               text-[9px]
                               font-bold
                               text-gray-900
                               flex items-center justify-center"
                    >
                        {{ $supportWaitingCount > 9 ? '9+' : $supportWaitingCount }}
                    </span>
                @endif
            </span>

            <span class="flex-1">
                Bantuan
            </span>

            @if($supportWaitingCount > 0)
                <span class="text-xs text-amber-400 font-semibold">
                    {{ $supportWaitingCount }}
                </span>
            @endif
        </a>

    </div>


    {{-- SISTEM --}}

    <div class="border-t border-white/10 py-2">

        <div
            class="px-4 py-1.5 text-xs
                   font-semibold text-gray-500
                   uppercase"
        >
            Sistem
        </div>

        <a
            href="{{ route('admin-kasirku.settings') }}"
            class="dropdown-link"
        >
            <span>⚙️</span>
            <span>Pengaturan</span>
        </a>

    </div>


@else

    {{-- ==============================
         OWNER / KASIR
         MENU LAMA
    =============================== --}}

    <div class="border-t border-white/10 py-2">

        <div
            class="px-4 py-1.5 text-xs
                   font-semibold text-gray-500
                   uppercase"
        >
            Navigasi
        </div>

        <a
            href="{{ route('dashboard') }}"
            class="dropdown-link"
        >
            <span>🏠</span>
            <span>Dashboard</span>
        </a>

        <a
            href="{{ route('kasir.index') }}"
            class="dropdown-link"
        >
            <span>🛒</span>
            <span>Kasir / keranjang</span>
        </a>

        <a
            href="{{ route('produk.index') }}"
            class="dropdown-link"
        >
            <span>📦</span>
            <span>Produk / restock produk</span>
        </a>
        
        <a
            href="{{ route('transfer.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('transfer.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span>🔄</span>
            <span>Transfer Antar Toko</span>
        </a>
        
        <a
            href="{{ route('pelanggan.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('pelanggan.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span>👥</span>
            <span>Pelanggan & Piutang</span>
        </a>

        <a
            href="{{ route('supplier.index') }}"
            class="dropdown-link"
        >
            <span>🚚</span>
            <span>Supplier & pembelian produk</span>
        </a>

        <a
            href="{{ route('pengeluaran') }}"
            class="dropdown-link"
        >
            <span>💸</span>
            <span>Pengeluaran</span>
        </a>

        <a
            href="{{ route('laporan') }}"
            class="dropdown-link"
        >
            <span>📊</span>
            <span>Laporan & Piutang</span>
        </a>

        <a
            href="{{ route('riwayat') }}"
            class="dropdown-link"
        >
            <span>🧾</span>
            <span>Riwayat Transaksi</span>
        </a>

        {{-- PUSAT BANTUAN --}}

        <a
            href="{{ route('support.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('support.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span class="relative">
                💬

                @if($supportWaitingCount > 0)
                    <span
                        class="absolute -top-1 -right-2
                               min-w-[16px] h-4
                               px-1
                               rounded-full
                               bg-amber-500
                               text-[9px]
                               font-bold
                               text-gray-900
                               flex items-center justify-center"
                    >
                        {{ $supportWaitingCount > 9 ? '9+' : $supportWaitingCount }}
                    </span>
                @endif
            </span>

            <span class="flex-1">
                Pusat Bantuan
            </span>

            @if($supportWaitingCount > 0)
                <span class="text-xs text-amber-400 font-semibold">
                    {{ $supportWaitingCount }} perlu respons
                </span>
            @endif
        </a>


        @if(session('logged_in'))

            <a
                href="{{ route('profil') }}"
                class="dropdown-link"
            >
                <span>👤</span>
                <span>Profil Saya</span>
            </a>

        @else

            <a
                href="{{ route('login') }}"
                class="dropdown-link"
            >
                <span>🔐</span>
                <span>Masuk / Daftar</span>
            </a>

        @endif

    </div>


    {{-- ADMIN TOKO --}}

    @if(session('user_role') === 'admin')

        <div class="border-t border-white/10 py-2">

            <div
                class="px-4 py-1.5 text-xs
                       font-semibold text-gray-500
                       uppercase"
            >
                Administrator
            </div>

            <a
                href="{{ route('admin.index') }}"
                class="dropdown-link"
            >
                <span>👥</span>
                <span>Manajemen Admin</span>
            </a>

            <a
                href="{{ route('setting') }}"
                class="dropdown-link"
            >
                <span>⚙️</span>
                <span>Pengaturan</span>
            </a>

        </div>

    @endif

@endif


{{-- LOGOUT DESKTOP --}}

@if(session('logged_in'))

    <div class="border-t border-white/10 py-2">

        <a
            href="{{ route('logout') }}"
            class="dropdown-link text-red-400
                   hover:bg-red-500/10
                   hover:text-red-300"
        >
            <span>🚪</span>
            <span>Keluar</span>
        </a>

    </div>

@endif

                    </div>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- MOBILE — RADIKAL --}}
            {{-- ================================================= --}}

            <div class="md:hidden flex items-center gap-1">

                @if(session('logged_in'))


                    {{-- ========================================= --}}
                    {{-- MOBILE STORE --}}
                    {{-- ========================================= --}}

                    @if($activeStore)

                        @if(session('user_role') === 'admin')

                            <div class="relative">

                                <button
                                    type="button"
                                    onclick="toggleMobileStoreDropdown(event)"
                                    class="flex items-center gap-1
                                           rounded-xl px-2 py-2
                                           hover:bg-gray-700 transition
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-emerald-500"
                                >

                                    <div class="text-right">

                                        <div
                                            class="text-sm font-semibold
                                                   text-emerald-400
                                                   max-w-[135px]
                                                   truncate"
                                        >
                                            🏪 {{ $activeStore->name }}
                                        </div>

                                        <div
                                            class="text-[10px]
                                                   text-gray-500"
                                        >
                                            Toko aktif
                                        </div>

                                    </div>

                                    <span
                                        class="text-xs text-gray-400"
                                    >
                                        ▾
                                    </span>

                                </button>


                                {{-- MOBILE STORE DROPDOWN --}}

                                <div
                                    id="mobile-store-dropdown"
                                    class="hidden absolute right-0 top-full mt-2
                                           w-60 rounded-2xl
                                           bg-gray-800
                                           border border-white/10
                                           shadow-2xl
                                           overflow-hidden z-[60]"
                                >

                                    <div
                                        class="px-4 py-3
                                               border-b border-white/10"
                                    >

                                        <div
                                            class="text-xs
                                                   text-gray-500
                                                   uppercase
                                                   font-semibold"
                                        >
                                            Pilih Toko
                                        </div>

                                    </div>


@php
    $storeLimit = $activeStore?->getLimit('max_stores');
@endphp

@foreach($layoutStores as $index => $store)

    @php
        $storeLocked =
            $storeLimit !== null &&
            $index >= $storeLimit;
    @endphp

    @if($storeLocked)

        {{-- TOKO TERKUNCI --}}
        <div
            class="dropdown-link
                   cursor-not-allowed
                   opacity-60"
        >

            <span>🏪</span>

            <span
                class="flex-1
                       text-left
                       truncate"
            >
                {{ $store->name }}
            </span>

            <span
                class="text-xs
                       text-amber-400
                       font-semibold"
            >
                🔒
            </span>

        </div>

    @else

        {{-- TOKO BISA DIAKSES --}}
        <form
            action="{{ route('store.switch', $store->id) }}"
            method="POST"
        >

            @csrf

            <button
                type="submit"
                class="dropdown-link"
            >

                <span>🏪</span>

                <span
                    class="flex-1
                           text-left
                           truncate"
                >
                    {{ $store->name }}
                </span>

                @if($store->id == $activeStoreId)

                    <span
                        class="text-emerald-400
                               font-bold"
                    >
                        ✓
                    </span>

                @endif

            </button>

        </form>

    @endif

@endforeach


                                    <a
                                        href="{{ route('store.create') }}"
                                        class="dropdown-link
                                               border-t border-white/10"
                                    >

                                        <span>➕</span>

                                        <span class="flex-1 text-left">
                                            Tambah Toko/Cabang
                                        </span>

                                    </a>

                                </div>

                            </div>

                        @else

                            {{-- MOBILE KASIR --}}

                            <div class="px-2">

                                <div
                                    class="text-sm font-semibold
                                           text-emerald-400
                                           max-w-[135px]
                                           truncate"
                                >
                                    🏪 {{ $activeStore->name }}
                                </div>

                                <div
                                    class="text-[10px]
                                           text-gray-500"
                                >
                                    Toko aktif
                                </div>

                            </div>

                        @endif

                    @endif



                    {{-- ========================================= --}}
                    {{-- MOBILE PROFILE BUTTON --}}
                    {{-- ========================================= --}}

                    <button
                        type="button"
                        onclick="toggleMobileMenu()"
                        class="flex items-center justify-center
                               w-10 h-10
                               rounded-xl
                               hover:bg-gray-700
                               transition
                               focus:outline-none
                               focus:ring-2
                               focus:ring-indigo-500"
                        aria-label="Menu profil"
                    >

                        <img
                            class="w-8 h-8 rounded-full
                                   border border-white/20
                                   object-cover"
                            src="{{ $avatarUrl }}"
                            alt="Profile"
                        >

                    </button>


                @else

                    {{-- ========================================= --}}
                    {{-- MOBILE GUEST --}}
                    {{-- ========================================= --}}

                    <button
                        onclick="toggleMobileMenu()"
                        type="button"
                        class="flex items-center justify-center
                               w-11 h-11 rounded-xl
                               hover:bg-gray-700 transition
                               focus:outline-none
                               focus:ring-2
                               focus:ring-indigo-500"
                        aria-label="Menu"
                    >

                        <svg
                            class="w-6 h-6 text-gray-300"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />

                        </svg>

                    </button>

                @endif

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- MOBILE PROFILE MENU --}}
    {{-- ========================================================= --}}

    <div
        id="mobile-menu"
        class="hidden absolute
               top-16 right-3 w-64 z-[63]"
    >

        <div
    class="bg-gray-800
           border border-white/10
           rounded-2xl
           shadow-2xl
           overflow-y-auto
           max-h-[75vh]"
    >


            {{-- ============================================= --}}
            {{-- PROFILE --}}
            {{-- ============================================= --}}

            @if(session('logged_in'))

                <div class="p-4">

                    <div class="flex items-center gap-3">

                        <img
                            class="w-11 h-11 rounded-full
                                   border border-white/20
                                   object-cover"
                            src="{{ $avatarUrl }}"
                            alt="Profile"
                        >

                        <div>

                            <div class="font-semibold text-white">
                                {{ session('username') }}
                            </div>

                            <div
                                class="text-xs text-gray-400 uppercase"
                            >
                                {{ session('user_role') }}
                            </div>

                        </div>

                    </div>

                </div>

            @endif
            
            {{-- ============================================= --}}
          {{-- INFORMASI PAKET MOBILE --}}
          {{-- ============================================= --}}
          
          @if(session('logged_in'))
          
              <div class="px-4 pb-4">
                   <x-plan-info :active-store="$activeStore" />
              </div>
          
          @endif

{{-- ============================================= --}}
{{-- NAVIGASI MOBILE --}}
{{-- ============================================= --}}

@if(session('is_platform_admin'))

    {{-- ========================================= --}}
    {{-- SUPER ADMIN MOBILE --}}
    {{-- ========================================= --}}

    <div class="border-t border-white/10 py-2">

        <div
            class="px-4 py-1.5 text-xs
                   font-semibold text-gray-500
                   uppercase"
        >
            Platform
        </div>


        <a
            href="{{ route('admin-kasirku.dashboard') }}"
            class="dropdown-link
                   {{ request()->routeIs('admin-kasirku.dashboard')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span>🏠</span>
            <span>Dashboard</span>
        </a>


        <a
            href="{{ route('admin-kasirku.users.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('admin-kasirku.users.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span>👥</span>
            <span>Pengguna</span>
        </a>


        <a
            href="{{ route('admin-kasirku.stores.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('admin-kasirku.stores.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span>🏪</span>
            <span>Toko</span>
        </a>


        <a
            href="{{ route('admin-kasirku.subscriptions.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('admin-kasirku.subscriptions.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span>💳</span>
            <span>Langganan</span>
        </a>


        <a
            href="{{ route('admin-kasirku.plans.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('admin-kasirku.plans.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span>📦</span>
            <span>Paket</span>
        </a>
        
        {{-- SEMUA AKTIVITAS --}}
          <a
              href="{{ route('admin-kasirku.audit-log.index') }}"
              class="mobile-bottom-item
                     {{ request()->routeIs('admin-kasirku.audit-log.*')
                          ? 'mobile-bottom-active'
                          : '' }}"
          >
              <span class="text-xl leading-none">
                  📋
              </span>
          
              <span>
                  Aktivitas
              </span>
          </a>

    </div>


    {{-- ========================================= --}}
    {{-- OPERASIONAL --}}
    {{-- ========================================= --}}

    <div class="border-t border-white/10 py-2">

        <div
            class="px-4 py-1.5 text-xs
                   font-semibold text-gray-500
                   uppercase"
        >
            Operasional
        </div>


        <a
            href="{{ route('admin-kasirku.support.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('admin-kasirku.support.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >

            <span class="relative">
                💬

                @if($supportWaitingCount > 0)
                    <span
                        class="absolute -top-1 -right-2
                               min-w-[16px] h-4
                               px-1
                               rounded-full
                               bg-amber-500
                               text-[9px]
                               font-bold
                               text-gray-900
                               flex items-center justify-center"
                    >
                        {{ $supportWaitingCount > 9 ? '9+' : $supportWaitingCount }}
                    </span>
                @endif
            </span>

            <span class="flex-1">
                Bantuan
            </span>

            @if($supportWaitingCount > 0)
                <span class="text-xs text-amber-400">
                    {{ $supportWaitingCount }}
                </span>
            @endif

        </a>

    </div>


    {{-- ========================================= --}}
    {{-- SISTEM --}}
    {{-- ========================================= --}}

    <div class="border-t border-white/10 py-2">

        <div
            class="px-4 py-1.5 text-xs
                   font-semibold text-gray-500
                   uppercase"
        >
            Sistem
        </div>


        <a
            href="{{ route('admin-kasirku.settings') }}"
            class="dropdown-link
                   {{ request()->routeIs('admin-kasirku.settings')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span>⚙️</span>
            <span>Pengaturan</span>
        </a>

    </div>


@else

    {{-- ========================================= --}}
    {{-- OWNER / KASIR MOBILE --}}
    {{-- ========================================= --}}

    <div class="border-t border-white/10 py-2">

        <div
            class="px-4 py-1.5 text-xs
                   font-semibold text-gray-500
                   uppercase"
        >
            Navigasi
        </div>


        <a
            href="{{ route('kasir.index') }}"
            class="dropdown-link"
        >
            <span>🛒</span>
            <span>Kasir / keranjang</span>
        </a>


        <a
            href="{{ route('produk.index') }}"
            class="dropdown-link"
        >
            <span>📦</span>
            <span>Produk / restock produk</span>
        </a>
        
        <a
            href="{{ route('transfer.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('transfer.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span>🔄</span>
            <span>Transfer Antar Toko</span>
        </a>
        
        <a
            href="{{ route('pelanggan.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('pelanggan.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >
            <span>👥</span>
            <span>Pelanggan & Piutang</span>
        </a>


        <a
            href="{{ route('supplier.index') }}"
            class="dropdown-link"
        >
            <span>🚚</span>
            <span>Supplier / pembelian produk</span>
        </a>


        <a
            href="{{ route('pengeluaran') }}"
            class="dropdown-link"
        >
            <span>💸</span>
            <span>Pengeluaran</span>
        </a>


        {{-- PUSAT BANTUAN --}}

        <a
            href="{{ route('support.index') }}"
            class="dropdown-link
                   {{ request()->routeIs('support.*')
                        ? 'bg-gray-700 text-white'
                        : '' }}"
        >

            <span class="relative">
                💬

                @if($supportWaitingCount > 0)
                    <span
                        class="absolute -top-1 -right-2
                               min-w-[16px] h-4
                               px-1
                               rounded-full
                               bg-amber-500
                               text-[9px]
                               font-bold
                               text-gray-900
                               flex items-center justify-center"
                    >
                        {{ $supportWaitingCount > 9 ? '9+' : $supportWaitingCount }}
                    </span>
                @endif
            </span>

            <span class="flex-1">
                Pusat Bantuan
            </span>

            @if($supportWaitingCount > 0)
                <span class="text-xs text-amber-400">
                    {{ $supportWaitingCount }}
                </span>
            @endif

        </a>


        @if(session('logged_in'))

            <a
                href="{{ route('profil') }}"
                class="dropdown-link"
            >
                <span>👤</span>
                <span>Profil Saya</span>
            </a>

        @else

            <a
                href="{{ route('login') }}"
                class="dropdown-link"
            >
                <span>🔐</span>
                <span>Masuk / Daftar</span>
            </a>

        @endif

    </div>


    {{-- ========================================= --}}
    {{-- ADMIN TOKO MOBILE --}}
    {{-- ========================================= --}}

    @if(session('user_role') === 'admin')

        <div class="border-t border-white/10 py-2">

            <div
                class="px-4 py-1.5 text-xs
                       font-semibold text-gray-500
                       uppercase"
            >
                Administrator
            </div>


            <a
                href="{{ route('admin.index') }}"
                class="dropdown-link"
            >
                <span>👥</span>
                <span>Manajemen Admin</span>
            </a>


            <a
                href="{{ route('setting') }}"
                class="dropdown-link"
            >
                <span>⚙️</span>
                <span>Pengaturan</span>
            </a>

        </div>

    @endif

@endif


{{-- ============================================= --}}
{{-- LOGOUT --}}
{{-- ============================================= --}}

@if(session('logged_in'))

    <div class="border-t border-white/10 py-2">

        <a
            href="{{ route('logout') }}"
            onclick="localStorage.removeItem('smart_pos_cart')"
            class="dropdown-link text-red-400
                   hover:bg-red-500/10
                   hover:text-red-300"
        >

            <span>🚪</span>
            <span>Keluar</span>

        </a>

    </div>

@endif

        </div>

    </div>

</nav>



{{-- ========================================================= --}}
{{-- HEADER HALAMAN --}}
{{-- ========================================================= --}}

<header
    class="relative bg-gray-800/50
           border-b border-white/10"
>

    <div
        class="mx-auto max-w-7xl
               px-4 py-3
               sm:px-6
               lg:px-8"
    >

        <div class="flex items-center gap-4">

            {{-- JUDUL --}}
            <h1
                class="text-2xl
                       sm:text-3xl
                       font-bold
                       tracking-tight
                       text-white
                       shrink-0"
            >
                @yield('header', 'Dashboard')
            </h1>


            {{-- FITUR HALAMAN --}}
            <div class="flex-1 min-w-0">

                @yield('header_tools')

            </div>

        </div>

    </div>

</header>



{{-- ========================================================= --}}
{{-- MAIN CONTENT --}}
{{-- ========================================================= --}}

<main class="flex-grow">

    <div
        class="mx-auto max-w-7xl
               px-4 py-6
               sm:px-6
               lg:px-8"
    >

        @if(session('error'))

            <div
                class="mb-5
                       rounded-xl
                       border border-red-500/20
                       bg-red-500/10
                       px-4 py-3
                       text-sm
                       text-red-300"
            >
                {{ session('error') }}
            </div>

        @endif

        @yield('content')

    </div>

</main>



{{-- ========================================================= --}}
{{-- FOOTER --}}
{{-- ========================================================= --}}

<footer
    class="mt-10 pt-6
                  border-t border-white/5
                  flex flex-col sm:flex-row
                  items-center
                  justify-between
                  gap-3"
>
          <a href="{{ route('register') }}"
             class="text-xs text-gray-600
                    hover:text-emerald-400
                    transition duration-200
                    cursor-pointer">
              © {{ date('Y') }} KasirKU. Semua hak dilindungi.
          </a>
      
          <p class="text-xs text-gray-600">
              Solusi kasir untuk usaha Anda.
          </p>
</footer>

{{-- ========================================================= --}}
{{-- GLOBAL QR SCANNER --}}
{{-- ========================================================= --}}

@if(
    session('logged_in') &&
    !request()->routeIs('dashboard') &&
    !request()->routeIs('kasir.index')
)

    <div
        id="globalQrScannerModal"
        class="fixed inset-0 z-[120]
               hidden items-center justify-center
               bg-black/80 p-4"
    >

        <div
            class="w-full max-w-md
                   rounded-2xl
                   bg-gray-800
                   border border-white/10
                   shadow-2xl
                   overflow-hidden"
        >

            {{-- HEADER --}}

            <div
                class="flex items-center justify-between
                       px-4 py-3
                       border-b border-white/10"
            >

                <div>

                    <h3 class="font-semibold text-white">
                        📷 Scan Produk
                    </h3>

                    <p class="text-xs text-gray-400">
                        Scan QR / barcode produk
                    </p>

                </div>


                <button
                    type="button"
                    onclick="closeGlobalQrScanner()"
                    class="w-9 h-9
                           flex items-center justify-center
                           rounded-xl
                           bg-gray-700
                           text-gray-300
                           hover:bg-gray-600"
                >
                    ✕
                </button>

            </div>


            {{-- CAMERA --}}

            <div class="p-4">

                <div
                    id="global-qr-reader"
                    class="w-full overflow-hidden rounded-xl bg-black"
                ></div>


                <div
                    id="global-qr-scan-status"
                    class="mt-3
                           text-sm text-gray-400
                           text-center"
                >
                    Menyiapkan kamera...
                </div>

            </div>


            {{-- FOOTER --}}

            <div class="px-4 pb-4">

                <button
                    type="button"
                    onclick="closeGlobalQrScanner()"
                    class="w-full
                           rounded-xl
                           bg-gray-700
                           hover:bg-gray-600
                           py-2.5
                           text-sm
                           font-semibold
                           text-white
                           transition"
                >
                    Tutup
                </button>

            </div>

        </div>

    </div>


    <script src="https://unpkg.com/html5-qrcode"></script>

@endif



{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

<script>


/*
|--------------------------------------------------------------------------
| DESKTOP PROFILE DROPDOWN
|--------------------------------------------------------------------------
*/

function toggleUserDropdown() {

    const dropdown =
        document.getElementById('user-dropdown');

    if (!dropdown) return;

    dropdown.classList.toggle('hidden');

}


/*
|--------------------------------------------------------------------------
| DESKTOP STORE DROPDOWN
|--------------------------------------------------------------------------
*/

function toggleStoreDropdown(event) {

    event.stopPropagation();

    const dropdown =
        document.getElementById('desktop-store-dropdown');

    if (!dropdown) return;

    dropdown.classList.toggle('hidden');

}


/*
|--------------------------------------------------------------------------
| MOBILE PROFILE MENU
|--------------------------------------------------------------------------
*/

function toggleMobileMenu() {

    const menu =
        document.getElementById('mobile-menu');

    if (!menu) return;

    menu.classList.toggle('hidden');

}

document.addEventListener('click', function (event) {
    const menu = document.getElementById('mobile-menu');

    if (!menu || menu.classList.contains('hidden')) {
        return;
    }

    const profileButton = event.target.closest(
        '[aria-label="Menu profil"]'
    );

    // Klik di luar dropdown
    if (!menu.contains(event.target) && !profileButton) {
        menu.classList.add('hidden');
    }
});
/*
|--------------------------------------------------------------------------
| MOBILE STORE DROPDOWN
|--------------------------------------------------------------------------
*/

function toggleMobileStoreDropdown(event) {

    event.stopPropagation();

    const dropdown =
        document.getElementById(
            'mobile-store-dropdown'
        );

    if (!dropdown) return;

    dropdown.classList.toggle('hidden');

}

/*
|--------------------------------------------------------------------------
| GLOBAL QR SCANNER
|--------------------------------------------------------------------------
| Dipakai halaman selain Dashboard dan Kasir.
| Tidak menggunakan openQrScanner() agar tidak bentrok.
|--------------------------------------------------------------------------
*/

let globalQrScanner = null;
let globalQrScanning = false;
let globalQrProcessing = false;

const globalScanUrlTemplate =
    @json(route('produk.scan', ['sku' => '__SKU__']));


function openGlobalQrScanner() {

    const modal =
        document.getElementById('globalQrScannerModal');

    const status =
        document.getElementById('global-qr-scan-status');

    if (!modal || !status) {
        console.error('Global QR Scanner tidak tersedia.');
        return;
    }


    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.body.classList.add('overflow-hidden');

    status.innerText =
        'Mengaktifkan kamera...';

    startGlobalQrScanner();

}


async function startGlobalQrScanner() {

    const status =
        document.getElementById('global-qr-scan-status');

    if (typeof Html5Qrcode === 'undefined') {

        if (status) {
            status.innerText =
                'Scanner gagal dimuat. Periksa koneksi internet.';
        }

        return;
    }


    if (globalQrScanning) {
        return;
    }


    globalQrProcessing = false;


    try {

        globalQrScanner =
            new Html5Qrcode('global-qr-reader');


        await globalQrScanner.start(

            { facingMode: 'environment' },

            {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 180
                }
            },

            function(decodedText) {

                handleGlobalScannedSku(decodedText);

            },

            function(errorMessage) {
                // Abaikan error scanning biasa
            }

        );


        globalQrScanning = true;


        if (status) {

            status.innerText =
                'Arahkan kamera ke QR / barcode produk.';

        }


    } catch (error) {

        console.error(
            'Global QR Scanner Error:',
            error
        );


        if (status) {

            status.innerText =
                'Kamera tidak dapat digunakan.';

        }

    }

}


async function handleGlobalScannedSku(decodedText) {

    if (globalQrProcessing) {
        return;
    }


    globalQrProcessing = true;


    const sku =
        String(decodedText || '').trim();


    if (!sku) {

        globalQrProcessing = false;

        return;

    }


    const status =
        document.getElementById(
            'global-qr-scan-status'
        );


    if (status) {

        status.innerText =
            'Mencari produk ' + sku + '...';

    }


    try {

        await stopGlobalQrScanner();


        const url =
            globalScanUrlTemplate.replace(
                '__SKU__',
                encodeURIComponent(sku)
            );


        const response =
            await fetch(
                url,
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );


        const data =
            await response.json();


        if (
            !response.ok ||
            !data.success
        ) {

            alert(
                data.message ||
                'Produk tidak ditemukan.'
            );

            globalQrProcessing = false;

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | PRODUK DITEMUKAN
        |--------------------------------------------------------------------------
        |
        | Simpan SKU ke URL Kasir.
        | Kasir akan mengambil produk tersebut
        | lalu memasukkannya ke cart.
        |
        */

        const kasirUrl =
            @json(route('kasir.index'));


        window.location.href =
            kasirUrl +
            '?scan_sku=' +
            encodeURIComponent(
                data.product.sku
            );


    } catch (error) {

        console.error(
            'Global QR Scanner Error:',
            error
        );


        alert(
            'Terjadi kesalahan saat membaca produk.'
        );


        globalQrProcessing = false;

    }

}


async function stopGlobalQrScanner() {

    if (
        globalQrScanner &&
        globalQrScanning
    ) {

        try {

            await globalQrScanner.stop();

            await globalQrScanner.clear();

        } catch (error) {

            console.warn(
                'Gagal menghentikan global scanner:',
                error
            );

        }

    }


    globalQrScanning = false;

}


async function closeGlobalQrScanner() {

    await stopGlobalQrScanner();


    globalQrProcessing = false;


    const modal =
        document.getElementById(
            'globalQrScannerModal'
        );


    if (modal) {

        modal.classList.add('hidden');

        modal.classList.remove('flex');

    }


    document.body.classList.remove(
        'overflow-hidden'
    );


    const reader =
        document.getElementById(
            'global-qr-reader'
        );


    if (reader) {

        reader.innerHTML = '';

    }


    const status =
        document.getElementById(
            'global-qr-scan-status'
        );


    if (status) {

        status.innerText =
            'Arahkan kamera ke QR / barcode produk.';

    }

}


/*
|--------------------------------------------------------------------------
| KLIK DI LUAR DROPDOWN
|--------------------------------------------------------------------------
*/

window.addEventListener('click', function (event) {


    /*
    |--------------------------------------------------------------------------
    | DESKTOP PROFILE
    |--------------------------------------------------------------------------
    */

    const userDropdown =
        document.getElementById('user-dropdown');

    if (userDropdown) {

        const userButton =
            userDropdown.parentElement
                ?.querySelector(
                    'button[onclick="toggleUserDropdown()"]'
                );

        if (
            !userDropdown.contains(event.target) &&
            !userButton?.contains(event.target)
        ) {

            userDropdown.classList.add('hidden');

        }

    }



    /*
    |--------------------------------------------------------------------------
    | DESKTOP STORE
    |--------------------------------------------------------------------------
    */

    const storeDropdown =
        document.getElementById(
            'desktop-store-dropdown'
        );

    if (storeDropdown) {

        const storeButton =
            storeDropdown.parentElement
                ?.querySelector(
                    'button[onclick="toggleStoreDropdown(event)"]'
                );

        if (
            !storeDropdown.contains(event.target) &&
            !storeButton?.contains(event.target)
        ) {

            storeDropdown.classList.add('hidden');

        }

    }



    /*
    |--------------------------------------------------------------------------
    | MOBILE STORE
    |--------------------------------------------------------------------------
    */

    const mobileStoreDropdown =
        document.getElementById(
            'mobile-store-dropdown'
        );

    if (mobileStoreDropdown) {

        const mobileStoreButton =
            mobileStoreDropdown.parentElement
                ?.querySelector(
                    'button[onclick="toggleMobileStoreDropdown(event)"]'
                );

        if (
            !mobileStoreDropdown.contains(event.target) &&
            !mobileStoreButton?.contains(event.target)
        ) {

            mobileStoreDropdown.classList.add('hidden');

        }

    }

});


/*
|--------------------------------------------------------------------------
| TUTUP MOBILE MENU SETELAH KLIK LINK
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll('#mobile-menu a')
    .forEach(function (link) {

        link.addEventListener('click', function () {

            const menu =
                document.getElementById('mobile-menu');

            if (menu) {

                menu.classList.add('hidden');

            }

        });

    });


</script>



{{-- ========================================================= --}}
{{-- STYLE --}}
{{-- ========================================================= --}}

<style>

.dropdown-link {

    display: flex;

    align-items: center;

    gap: 0.75rem;

    width: 100%;

    padding: 0.65rem 1rem;

    font-size: 0.875rem;

    font-weight: 500;

    color: rgb(209 213 219);

    transition: all 0.2s ease;

}


.dropdown-link:hover {

    background: rgb(55 65 81);

    color: white;

}

.mobile-bottom-item {

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    gap: 3px;

    min-width: 58px;

    height: 58px;

    border-radius: 14px;

    font-size: 10px;

    font-weight: 600;

    color: rgb(156 163 175);

    transition: all 0.2s ease;

}


.mobile-bottom-item:hover {

    color: white;

    background: rgb(31 41 55 / 0.7);

}


.mobile-bottom-active {

    color: rgb(52 211 153);

}


.mobile-bottom-action {

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    gap: 2px;

    width: 58px;

    height: 58px;

    margin-top: -18px;

    border-radius: 18px;

    background: rgb(16 185 129);

    color: white;

    font-size: 10px;

    font-weight: 700;

    border: 4px solid rgb(17 24 39);

    box-shadow:
        0 8px 25px rgb(16 185 129 / 0.25);

    transition: all 0.2s ease;

}


.mobile-bottom-action:hover {

    transform: translateY(-2px);

}

</style>

{{-- ========================================================= --}}
{{-- MOBILE BOTTOM NAVIGATION --}}
{{-- ========================================================= --}}

@if(session('logged_in'))

    @php
        $mobileAction = trim($__env->yieldContent('mobile_action', 'scan'));
    @endphp

    <nav
        class="md:hidden fixed bottom-0 left-0 right-0 z-[70]
               bg-gray-900/95 backdrop-blur-xl
               border-t border-white/10
               shadow-2xl"
    >

        <div
            class="mx-auto max-w-md
                   h-[68px]
                   px-2
                   flex items-center justify-around"
        >

            {{-- ================================================= --}}
            {{-- ADMIN KASIRKU --}}
            {{-- ================================================= --}}

            @if(request()->routeIs('admin-kasirku.*'))

                {{-- DASHBOARD --}}
                <a
                    href="{{ route('admin-kasirku.dashboard') }}"
                    class="mobile-bottom-item
                           {{ request()->routeIs('admin-kasirku.dashboard')
                                ? 'mobile-bottom-active'
                                : '' }}"
                >

                    <span class="text-xl leading-none">
                        🏠
                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                {{-- PENGGUNA --}}
                <a
                    href="{{ route('admin-kasirku.users.index') }}"
                    class="mobile-bottom-item
                           {{ request()->routeIs('admin-kasirku.users.*')
                                ? 'mobile-bottom-active'
                                : '' }}"
                >

                    <span class="text-xl leading-none">
                        👤
                    </span>

                    <span>
                        Pengguna
                    </span>

                </a>


                {{-- TOKO --}}
                <a
                    href="{{ route('admin-kasirku.stores.index') }}"
                    class="mobile-bottom-item
                           {{ request()->routeIs('admin-kasirku.stores.*')
                                ? 'mobile-bottom-active'
                                : '' }}"
                >

                    <span class="text-xl leading-none">
                        🏪
                    </span>

                    <span>
                        Toko
                    </span>

                </a>


                {{-- LANGGANAN --}}
                <a
                    href="{{ route('admin-kasirku.subscriptions.index') }}"
                    class="mobile-bottom-item
                           {{ request()->routeIs('admin-kasirku.subscriptions.*')
                                ? 'mobile-bottom-active'
                                : '' }}"
                >

                    <span class="text-xl leading-none">
                        💳
                    </span>

                    <span>
                        Langganan
                    </span>

                </a>


                {{-- PAKET --}}
                <a
                    href="{{ route('admin-kasirku.plans.index') }}"
                    class="mobile-bottom-item
                           {{ request()->routeIs('admin-kasirku.plans.*')
                                ? 'mobile-bottom-active'
                                : '' }}"
                >

                    <span class="text-xl leading-none">
                        📦
                    </span>

                    <span>
                        Paket
                    </span>

                </a>


            @else

                {{-- ================================================= --}}
                {{-- KASIRKU / TOKO --}}
                {{-- ================================================= --}}

                {{-- BERANDA --}}
                <a
                    href="{{ route('dashboard') }}"
                    class="mobile-bottom-item
                           {{ request()->routeIs('dashboard')
                                ? 'mobile-bottom-active'
                                : '' }}"
                >

                    <span class="text-xl leading-none">
                        🏠
                    </span>

                    <span>
                        Beranda
                    </span>

                </a>


                {{-- RIWAYAT --}}
                <a
                    href="{{ route('riwayat') }}"
                    class="mobile-bottom-item
                           {{ request()->routeIs('riwayat')
                                ? 'mobile-bottom-active'
                                : '' }}"
                >

                    <span class="text-xl leading-none">
                        🧾
                    </span>

                    <span>
                        Riwayat Transaksi
                    </span>

                </a>


                {{-- QUICK ACTION --}}
                @if($mobileAction === 'tambah')

                    <button
                        type="button"
                        onclick="toggleProductModal()"
                        class="mobile-bottom-action"
                    >

                        <span>
                            ➕
                        </span>

                        <span>
                            Tambah
                        </span>

                    </button>

                @elseif($mobileAction === 'kasir')

                    <a
                        href="{{ route('kasir.index') }}"
                        class="mobile-bottom-action"
                    >

                        <span>
                            🛒
                        </span>

                        <span>
                            Kasir
                        </span>

                    </a>

                @elseif($mobileAction === 'scan')

                    @if(
                        request()->routeIs('dashboard') ||
                        request()->routeIs('kasir.index')
                    )

                        {{-- Scanner Dashboard / Kasir --}}
                        <button
                            type="button"
                            onclick="openQrScanner()"
                            class="mobile-bottom-action"
                        >

                            <span>
                                📷
                            </span>

                            <span>
                                Scan
                            </span>

                        </button>

                    @else

                        {{-- Scanner Global --}}
                        <button
                            type="button"
                            onclick="openGlobalQrScanner()"
                            class="mobile-bottom-action"
                        >

                            <span>
                                📷
                            </span>

                            <span>
                                Scan
                            </span>

                        </button>

                    @endif

                @else

                    {{-- DEFAULT --}}
                    <button
                        type="button"
                        onclick="openGlobalQrScanner()"
                        class="mobile-bottom-action"
                    >

                        <span>
                            📷
                        </span>

                        <span>
                            Scan
                        </span>

                    </button>

                @endif


                {{-- LAPORAN --}}
                <a
                    href="{{ route('laporan') }}"
                    class="mobile-bottom-item
                           {{ request()->routeIs('laporan')
                                ? 'mobile-bottom-active'
                                : '' }}"
                >

                    <span class="text-xl leading-none">
                        📊
                    </span>

                    <span>
                        Laporan & Piutang
                    </span>

                </a>


{{-- BANTUAN --}}
<a
    href="{{ route('support.index') }}"
    class="mobile-bottom-item
           relative
           {{ request()->routeIs('support.*')
                ? 'mobile-bottom-active'
                : '' }}"
>

    <span class="relative text-xl leading-none">

        💬

        @if($supportWaitingCount > 0)
            <span
                class="absolute
                       -top-1 -right-2
                       min-w-[15px] h-[15px]
                       px-1
                       rounded-full
                       bg-amber-500
                       text-[9px]
                       font-bold
                       text-gray-900
                       flex items-center justify-center"
            >
                {{ $supportWaitingCount > 9 ? '9+' : $supportWaitingCount }}
            </span>
        @endif

    </span>

    <span>
        Bantuan
    </span>

</a>

            @endif

        </div>

    </nav>

@endif

<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js')
            .then(function (registration) {
                console.log(
                    'KasirKU Service Worker aktif:',
                    registration.scope
                );
            })
            .catch(function (error) {
                console.error(
                    'KasirKU Service Worker gagal:',
                    error
                );
            });
    });
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const splash = document.getElementById('kasirku-splash');

    if (!splash) {
        return;
    }

    const splashShown = sessionStorage.getItem(
        'kasirku_splash_shown'
    );

    /*
     * Kalau splash sudah pernah ditampilkan
     * dalam sesi aplikasi ini, jangan tampilkan lagi.
     */
    if (splashShown) {
        splash.remove();
        return;
    }

    /*
     * Tandai bahwa splash sudah ditampilkan.
     */
    sessionStorage.setItem(
        'kasirku_splash_shown',
        '1'
    );

    /*
     * Tampilkan splash.
     */
    splash.classList.remove('hidden');

    /*
     * Tunggu sebentar sebelum fade out.
     */
    setTimeout(function () {

        splash.classList.remove('opacity-0');
        splash.classList.add('opacity-100');

    }, 50);

    setTimeout(function () {

        splash.classList.remove('opacity-100');
        splash.classList.add('opacity-0');

        setTimeout(function () {
            splash.remove();
        }, 500);

    }, 750);

});
</script>

</body>

</html>