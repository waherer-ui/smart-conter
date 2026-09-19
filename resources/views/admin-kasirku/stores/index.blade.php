@extends('layouts.app')

@section('title', 'Detail Owner & Toko')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- =========================================================
         1. TABEL / DAFTAR OWNER (BAGIAN ATAS - BISA DIKLIK)
    ========================================================== --}}
    <div class="mb-8 bg-gray-800/80 border border-gray-700 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    👥 Pilih Owner Toko
                </h2>
                <p class="text-xs text-gray-400">
                    Klik pada salah satu baris owner di bawah untuk melihat detail langganan dan toko-tokonya.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-300">
                <thead class="text-xs text-gray-400 uppercase bg-gray-900/60 border-b border-gray-700">
                    <tr>
                        <th class="px-4 py-3">Nama Owner</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3 text-center">Jumlah Toko</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/60">
                    @forelse($owners as $owner)
                        @php
                            $isSelected = $selectedOwner && $selectedOwner->id === $owner->id;
                        @endphp
                        <tr class="hover:bg-gray-700/40 transition {{ $isSelected ? 'bg-emerald-500/10 border-l-4 border-emerald-500' : '' }}">
                            <td class="px-4 py-3.5 font-semibold text-white">
                                {{ $owner->name }}
                            </td>
                            <td class="px-4 py-3.5 text-gray-400">
                                {{ $owner->email }}
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-emerald-400">
                                {{ $owner->stores_count }} Toko
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('admin-kasirku.stores.index', ['owner_id' => $owner->id]) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $isSelected ? 'bg-emerald-500 text-white' : 'bg-gray-700 hover:bg-gray-600 text-gray-200' }}">
                                    {{ $isSelected ? '✓ Sedang Dilihat' : 'Lihat Detail →' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                Belum ada data Owner.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($owners->hasPages())
            <div class="mt-4">
                {{ $owners->links() }}
            </div>
        @endif
    </div>


    {{-- =========================================================
         2. TAMPILAN DETAIL OWNER & TOKO (BAGIAN BAWAH)
    ========================================================== --}}
    @if($selectedOwner)

        {{-- HEADER INFO OWNER --}}
        <div class="mb-6 bg-gray-800/40 border border-gray-700/60 rounded-2xl p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-2xl font-bold text-emerald-400">
                        {{ strtoupper(substr($selectedOwner->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            {{ $selectedOwner->name }}
                        </h1>
                        <p class="text-sm text-gray-400">
                            {{ $selectedOwner->email }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-xs font-semibold text-indigo-400">
                        OWNER
                    </span>

                    @if($selectedOwner->subscription && $selectedOwner->subscription->status === 'active')
                        <span class="px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-xs font-semibold text-emerald-400">
                            🟢 Aktif
                        </span>
                    @else
                        <span class="px-3 py-1.5 rounded-full bg-gray-500/10 border border-gray-500/20 text-xs font-semibold text-gray-400">
                            Tidak Aktif
                        </span>
                    @endif
                </div>
            </div>
        </div>

    {{-- RINGKASAN OWNER STATS (SUDAH AMAN DARI ERROR NULL) --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <span class="text-2xl">🏪</span>
            <span class="text-xs text-gray-500">Toko</span>
        </div>
        <div class="mt-3">
            <p class="text-2xl font-bold text-white">{{ $selectedOwner->stores?->count() ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">Total toko</p>
        </div>
    </div>

    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <span class="text-2xl">👥</span>
            <span class="text-xs text-gray-500">Staf</span>
        </div>
        <div class="mt-3">
            <p class="text-2xl font-bold text-white">{{ $selectedOwner->stores->sum(fn($s) => $s->users?->count() ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total staf</p>
        </div>
    </div>

    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <span class="text-2xl">📦</span>
            <span class="text-xs text-gray-500">Produk</span>
        </div>
        <div class="mt-3">
            <p class="text-2xl font-bold text-white">{{ $selectedOwner->stores->sum(fn($s) => $s->products?->count() ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Semua toko</p>
        </div>
    </div>

    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <span class="text-2xl">👤</span>
            <span class="text-xs text-gray-500">Pelanggan</span>
        </div>
        <div class="mt-3">
            <p class="text-2xl font-bold text-white">{{ $selectedOwner->stores->sum(fn($s) => $s->customers?->count() ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Semua toko</p>
        </div>
    </div>
</div>


        {{-- LANGGANAN OWNER --}}
        <div class="bg-gray-800/80 border border-gray-700 rounded-2xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-gray-700">
                <h2 class="text-sm font-bold text-white uppercase tracking-wide">Langganan</h2>
            </div>
            <div class="p-5">
                @if($selectedOwner->subscription)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Paket</p>
                            <p class="text-lg font-bold text-white">{{ $selectedOwner->subscription->plan->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Harga</p>
                            <p class="text-lg font-bold text-emerald-400">
                                Rp{{ number_format($selectedOwner->subscription->plan->price ?? 0, 0, ',', '.') }}
                                <span class="text-xs font-normal text-gray-500">/ bulan</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Status</p>
                            @if($selectedOwner->subscription->status === 'active')
                                <p class="font-semibold text-emerald-400">🟢 Aktif</p>
                            @else
                                <p class="font-semibold text-gray-400">{{ ucfirst($selectedOwner->subscription->status) }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Mulai</p>
                            <p class="font-semibold text-white">{{ optional($selectedOwner->subscription->starts_at)->format('d M Y') ?? '-' }}</p>
                        </div>
                    </div>
                @else
                    <div class="py-6 text-center">
                        <div class="text-3xl mb-2">💳</div>
                        <p class="text-gray-400 text-sm">Owner belum memiliki langganan.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- DAFTAR TOKO MILIK OWNER --}}
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-white">🏪 Toko Milik Owner</h2>
                <p class="text-sm text-gray-400 mt-1">Seluruh toko yang terhubung dengan akun owner ini.</p>
            </div>
            <span class="text-xs text-gray-500">{{ $selectedOwner->stores->count() }} Toko</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            @forelse($selectedOwner->stores as $store)
                <div class="bg-gray-800/80 border border-gray-700 rounded-2xl overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="p-5 border-b border-gray-700 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-xl">
                                    🏪
                                </div>
                                <div>
                                    <h3 class="font-bold text-white">{{ $store->name }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">ID #{{ $store->id }}</p>
                                </div>
                            </div>
                            @if($store->is_active ?? true)
                                <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-xs font-semibold text-emerald-400">🟢 Aktif</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-xs font-semibold text-red-400">🔴 Tidak Aktif</span>
                            @endif
                        </div>

                        {{-- STAF TOKO --}}
                        <div class="p-5 border-b border-gray-700">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Staf Toko</h4>
                            <div class="space-y-2">
                                @forelse($store->users as $staff)
                                    <div class="flex items-center justify-between bg-gray-900/50 rounded-xl px-3 py-2">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-300">
                                                {{ strtoupper(substr($staff->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-medium text-white">{{ $staff->name }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $staff->pivot->role ?? 'Kasir' }}</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-500">Belum ada staf.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- INFORMASI TOKO --}}
                        <div class="p-5 border-b border-gray-700 space-y-2 text-sm">
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Alamat</span>
                                <span class="text-gray-200 text-right">{{ $store->address ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span class="text-gray-500">Telepon</span>
                                <span class="text-gray-200 text-right">{{ $store->phone ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- RINGKASAN DATA --}}
                        <div class="p-5">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="bg-gray-900/60 rounded-xl p-3">
                                    <p class="text-xs text-gray-500">Produk</p>
                                    <p class="text-lg font-bold text-white mt-1">{{ $store->products->count() }}</p>
                                </div>
                                <div class="bg-gray-900/60 rounded-xl p-3">
                                    <p class="text-xs text-gray-500">Pelanggan</p>
                                    <p class="text-lg font-bold text-white mt-1">{{ $store->customers->count() }}</p>
                                </div>
                                <div class="bg-gray-900/60 rounded-xl p-3">
                                    <p class="text-xs text-gray-500">Transaksi</p>
                                    <p class="text-lg font-bold text-white mt-1">{{ $store->transactions->count() }}</p>
                                </div>
                                <div class="bg-gray-900/60 rounded-xl p-3">
                                    <p class="text-xs text-gray-500">Staf</p>
                                    <p class="text-lg font-bold text-white mt-1">{{ $store->users->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="lg:col-span-2 bg-gray-800/80 border border-gray-700 rounded-2xl p-10 text-center">
                    <div class="text-4xl mb-3">🏪</div>
                    <p class="text-white font-semibold">Belum ada toko</p>
                    <p class="text-sm text-gray-500 mt-1">Owner ini belum memiliki toko.</p>
                </div>
            @endforelse
        </div>

    @endif

</div>

@endsection
