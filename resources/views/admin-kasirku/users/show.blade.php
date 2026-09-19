@extends('layouts.app')

@section('title', 'Detail Owner')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">{{-- =========================================================
     KEMBALI
========================================================== --}}
<a
    href="{{ route('admin-kasirku.users.index') }}"
    class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition"
>
    ← Kembali ke Pengguna
</a>


{{-- =========================================================
     PROFILE OWNER
========================================================== --}}
<div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

    <div class="flex flex-col sm:flex-row sm:items-center gap-4">

        {{-- Avatar --}}
        <div
            class="w-16 h-16 shrink-0 rounded-2xl
                   bg-emerald-500/20 border border-emerald-500/30
                   flex items-center justify-center
                   text-2xl font-bold text-emerald-400"
        >
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        {{-- Informasi Owner --}}
        <div class="flex-1 min-w-0">

            <div class="flex flex-wrap items-center gap-2">

                <h1 class="text-xl font-bold text-white">
                    {{ $user->name }}
                </h1>

                <span
                    class="px-2 py-1 text-xs rounded-lg
                           bg-indigo-500/20 text-indigo-300"
                >
                    OWNER
                </span>

                <span
                    class="px-2 py-1 text-xs rounded-lg
                           bg-emerald-500/20 text-emerald-300"
                >
                    🟢 Aktif
                </span>

            </div>

            <p class="text-sm text-gray-400 mt-1 truncate">
                {{ $user->email }}
            </p>

        </div>

    </div>

</div>


{{-- =========================================================
     RINGKASAN OWNER
========================================================== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

    {{-- Toko --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-xs text-gray-400 uppercase">
                    Toko
                </p>

                <p class="text-2xl font-bold text-white mt-1">
                    {{ $user->ownedStores->count() }}
                </p>
            </div>

            <div class="text-2xl opacity-80">
                🏪
            </div>

        </div>

    </div>


    {{-- Pengguna --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-xs text-gray-400 uppercase">
                    Pengguna
                </p>

                <p class="text-2xl font-bold text-white mt-1">
                    {{ $user->ownedStores->sum(fn ($store) => $store->users->count()) }}
                </p>
            </div>

            <div class="text-2xl opacity-80">
                👥
            </div>

        </div>

    </div>


    {{-- Produk --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-xs text-gray-400 uppercase">
                    Produk
                </p>

                <p class="text-2xl font-bold text-white mt-1">
                    {{ $user->ownedStores->sum(fn ($store) => $store->products->count()) }}
                </p>
            </div>

            <div class="text-2xl opacity-80">
                📦
            </div>

        </div>

    </div>


    {{-- Pelanggan --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-xs text-gray-400 uppercase">
                    Pelanggan
                </p>

                <p class="text-2xl font-bold text-white mt-1">
                    {{ $user->ownedStores->sum(fn ($store) => $store->customers->count()) }}
                </p>
            </div>

            <div class="text-2xl opacity-80">
                👤
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     LANGGANAN OWNER
========================================================== --}}
<div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

    <div class="flex items-center justify-between gap-3 mb-5">

        <div>
            <h2 class="text-lg font-semibold text-white">
                💳 Langganan Owner
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                Paket yang digunakan oleh akun owner ini
            </p>
        </div>

    </div>


    @if($user->subscription && $user->subscription->plan)

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            {{-- Paket --}}
            <div class="bg-gray-900/50 rounded-xl p-4">

                <p class="text-xs text-gray-500 uppercase">
                    Paket
                </p>

                <p class="text-white font-semibold mt-1">
                    {{ $user->subscription->plan->name }}
                </p>

            </div>


            {{-- Harga --}}
            <div class="bg-gray-900/50 rounded-xl p-4">

                <p class="text-xs text-gray-500 uppercase">
                    Harga
                </p>

                <p class="text-white font-semibold mt-1">
                    Rp{{ number_format($user->subscription->plan->price, 0, ',', '.') }}
                </p>

                <p class="text-xs text-gray-500 mt-1">
                    / bulan
                </p>

            </div>


            {{-- Status --}}
            <div class="bg-gray-900/50 rounded-xl p-4">

                <p class="text-xs text-gray-500 uppercase">
                    Status
                </p>

                @php
                    $status = $user->subscription->status;
                @endphp

                <p class="mt-1 font-semibold
                    {{ $status === 'active'
                        ? 'text-emerald-400'
                        : 'text-gray-300' }}"
                >
                    {{ $status === 'active' ? '🟢 Aktif' : ucfirst($status) }}
                </p>

            </div>


            {{-- Mulai --}}
            <div class="bg-gray-900/50 rounded-xl p-4">

                <p class="text-xs text-gray-500 uppercase">
                    Mulai
                </p>

                <p class="text-white font-semibold mt-1">
                    {{ $user->subscription->starts_at
                        ? $user->subscription->starts_at->format('d M Y')
                        : '-' }}
                </p>

            </div>

        </div>

    @else

        <div class="bg-gray-900/50 rounded-xl p-4">

            <p class="text-sm text-gray-400">
                Owner ini belum memiliki data langganan.
            </p>

        </div>

    @endif

</div>


{{-- =========================================================
     TOKO MILIK OWNER
========================================================== --}}
<div>

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">

        <div>

            <h2 class="text-lg font-bold text-white">
                🏪 Toko Milik Owner
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                {{ $user->ownedStores->count() }} toko terdaftar
            </p>

        </div>

    </div>

</div>


{{-- =========================================================
     DAFTAR TOKO
========================================================== --}}
@if($user->ownedStores->count())

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

        @foreach($user->ownedStores as $store)

            <div
                class="bg-gray-800/80 border border-white/10
                       rounded-2xl overflow-hidden shadow-xl"
            >

                {{-- HEADER TOKO --}}
                <div class="p-5 border-b border-white/10">

                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">

                            <div class="flex items-center gap-2">

                                <span class="text-xl">
                                    🏪
                                </span>

                                <h3 class="text-lg font-bold text-white truncate">
                                    {{ $store->name }}
                                </h3>

                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                ID #{{ $store->id }}
                            </p>

                        </div>


                        {{-- Status Toko --}}
                        <span
                            class="shrink-0 px-2 py-1 rounded-lg text-xs
                                   bg-emerald-500/20 text-emerald-300"
                        >
                            🟢 Aktif
                        </span>

                    </div>

                </div>


                {{-- ISI TOKO --}}
                <div class="p-5 space-y-5">

                    {{-- Informasi Kontak --}}
                    <div>

                        <h4 class="text-xs font-semibold text-gray-400 mb-3">
                            INFORMASI TOKO
                        </h4>

                        <div class="space-y-2 text-sm">

                            <div class="flex justify-between gap-4">

                                <span class="text-gray-500">
                                    Alamat
                                </span>

                                <span class="text-gray-200 text-right">
                                    {{ $store->address ?: '-' }}
                                </span>

                            </div>

                            <div class="flex justify-between gap-4">

                                <span class="text-gray-500">
                                    Telepon
                                </span>

                                <span class="text-gray-200 text-right">
                                    {{ $store->phone ?: '-' }}
                                </span>

                            </div>

                        </div>

                    </div>


                    {{-- Ringkasan Toko --}}
                    <div>

                        <h4 class="text-xs font-semibold text-gray-400 mb-3">
                            RINGKASAN TOKO
                        </h4>

                        <div class="grid grid-cols-2 gap-3">

                            <div class="bg-gray-900/60 rounded-xl p-3">

                                <p class="text-xs text-gray-500">
                                    Produk
                                </p>

                                <p class="text-lg font-bold text-white">
                                    {{ $store->products->count() }}
                                </p>

                            </div>


                            <div class="bg-gray-900/60 rounded-xl p-3">

                                <p class="text-xs text-gray-500">
                                    Pelanggan
                                </p>

                                <p class="text-lg font-bold text-white">
                                    {{ $store->customers->count() }}
                                </p>

                            </div>


                            <div class="bg-gray-900/60 rounded-xl p-3">

                                <p class="text-xs text-gray-500">
                                    Transaksi
                                </p>

                                <p class="text-lg font-bold text-white">
                                    {{ $store->transactions->count() }}
                                </p>

                            </div>


                            <div class="bg-gray-900/60 rounded-xl p-3">

                                <p class="text-xs text-gray-500">
                                    Pengguna
                                </p>

                                <p class="text-lg font-bold text-white">
                                    {{ $store->users->count() }}
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- Detail Toko --}}
                    <div class="pt-1">

                        <a
                            href="{{ route('admin-kasirku.stores.show', $store) }}"
                            class="block w-full text-center px-4 py-3
                                   rounded-xl
                                   bg-emerald-600 hover:bg-emerald-500
                                   text-white font-semibold text-sm
                                   transition"
                        >
                            Lihat Detail Toko →
                        </a>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

@else

    {{-- Tidak Ada Toko --}}
    <div
        class="bg-gray-800/80 border border-white/10
               rounded-2xl p-8 text-center"
    >

        <div class="text-3xl mb-3">
            🏪
        </div>

        <p class="text-white font-semibold">
            Belum ada toko
        </p>

        <p class="text-sm text-gray-500 mt-1">
            Owner ini belum memiliki toko terdaftar.
        </p>

    </div>

@endif

</div>@endsection