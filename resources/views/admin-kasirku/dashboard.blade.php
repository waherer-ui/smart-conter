@extends('layouts.app')

@section('title', 'Admin KasirKU')
@section('header', 'Admin KasirKU')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-white">
            Dashboard Admin KasirKU
        </h1>

        <p class="text-sm text-gray-400 mt-1">
            Pantau pengguna, toko, dan langganan KasirKU.
        </p>
    </div>
    
{{-- NAVIGASI ADMIN PLATFORM --}}
<div class="flex flex-wrap gap-2 mt-4">

    {{-- DASHBOARD --}}
    <a
        href="{{ route('admin-kasirku.dashboard') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
               bg-gray-700 hover:bg-gray-600
               text-white text-sm font-medium transition"
    >
        📊 Dashboard
    </a>

    {{-- PENGGUNA --}}
    <a
        href="{{ route('admin-kasirku.users.index') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
               bg-gray-700 hover:bg-gray-600
               text-white text-sm font-medium transition"
    >
        👤 Pengguna
    </a>

    {{-- TOKO --}}
    <a
        href="{{ route('admin-kasirku.stores.index') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
               bg-gray-700 hover:bg-gray-600
               text-white text-sm font-medium transition"
    >
        🏪 Toko
    </a>

    {{-- LANGGANAN --}}
    <a
        href="{{ route('admin-kasirku.subscriptions.index') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
               bg-gray-700 hover:bg-gray-600
               text-white text-sm font-medium transition"
    >
        💳 Langganan
    </a>

    {{-- PAKET --}}
    <a
        href="{{ route('admin-kasirku.plans.index') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
               bg-gray-700 hover:bg-gray-600
               text-white text-sm font-medium transition"
    >
        📦 Paket
    </a>

    {{-- PENGATURAN --}}
    <a
        href="{{ route('admin-kasirku.settings') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
               bg-gray-700 hover:bg-gray-600
               text-white text-sm font-medium transition"
    >
        ⚙️ Pengaturan
    </a>

</div>


    {{-- STATISTIK --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        {{-- USERS --}}
        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">
                        Total Pengguna
                    </p>

                    <p class="text-3xl font-bold text-white mt-2">
                        {{ number_format($totalUsers, 0, ',', '.') }}
                    </p>
                </div>

                <div class="text-3xl">
                    👤
                </div>
            </div>
        </div>


        {{-- STORES --}}
        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">
                        Total Toko
                    </p>

                    <p class="text-3xl font-bold text-white mt-2">
                        {{ number_format($totalStores, 0, ',', '.') }}
                    </p>
                </div>

                <div class="text-3xl">
                    🏪
                </div>
            </div>
        </div>


        {{-- SUBSCRIPTIONS --}}
        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400">
                        Langganan Aktif
                    </p>

                    <p class="text-3xl font-bold text-white mt-2">
                        {{ number_format($activeSubscriptions, 0, ',', '.') }}
                    </p>
                </div>

                <div class="text-3xl">
                    💳
                </div>
            </div>
        </div>

    </div>


    {{-- INFORMASI --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

        <h2 class="text-lg font-semibold text-white">
            Admin KasirKU
        </h2>

        <p class="text-sm text-gray-400 mt-2">
            Gunakan dashboard ini untuk mengelola platform KasirKU.
        </p>

    </div>

</div>

@endsection
