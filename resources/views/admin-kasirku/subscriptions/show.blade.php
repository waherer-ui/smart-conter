@extends('layouts.app')

@section('title', 'Detail Langganan')
@section('header', 'Langganan')

@section('content')

<div class="space-y-6">{{-- HEADER --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

    <div>
        <h1 class="text-2xl font-bold text-white">
            Detail Langganan
        </h1>

        <p class="text-sm text-gray-400 mt-1">
            Informasi lengkap langganan pemilik akun KasirKU.
        </p>
    </div>

    <a
        href="{{ route('admin-kasirku.subscriptions.index') }}"
        class="inline-flex items-center justify-center gap-2
               px-4 py-2 rounded-xl
               bg-gray-700 hover:bg-gray-600
               text-white text-sm font-medium transition"
    >
        ← Kembali
    </a>

</div>


{{-- INFORMASI PEMILIK & TOKO --}}
<div class="bg-gray-800/80 border border-white/10
            rounded-2xl p-5 shadow-xl">

    <h2 class="text-lg font-semibold text-white mb-4">
        Informasi Pemilik & Toko
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        {{-- PEMILIK --}}
        <div>

            <p class="text-xs text-gray-500 mb-1">
                Pemilik Akun
            </p>

            @if($subscription->owner)

                <p class="text-white font-medium">
                    {{ $subscription->owner->name }}
                </p>

                <p class="text-xs text-gray-500 mt-1">
                    {{ $subscription->owner->email }}
                </p>

            @else

                <p class="text-gray-500">
                    Tidak ditemukan
                </p>

            @endif

        </div>


        {{-- JUMLAH PENGGUNA UNIK --}}
        <div>

            <p class="text-xs text-gray-500 mb-1">
                Pengguna
            </p>

            @php
                $uniqueUsers = $subscription->owner?->stores
                    ?->flatMap(fn ($store) => $store->users)
                    ->unique('id')
                    ->count() ?? 0;
            @endphp

            <p class="text-gray-300">
                {{ $uniqueUsers }} pengguna
            </p>

        </div>


        {{-- TOKO --}}
        <div class="sm:col-span-2">

            <p class="text-xs text-gray-500 mb-2">
                Toko Milik Owner
            </p>

            @if($subscription->owner?->stores?->count())

                <div class="space-y-2">

                    @foreach($subscription->owner->stores as $store)

                        <div class="flex items-center justify-between
                                    bg-gray-900/50
                                    border border-white/5
                                    rounded-xl
                                    px-3 py-2">

                            <div class="flex items-center gap-3">

                                <div class="w-8 h-8 rounded-lg
                                            bg-blue-500/10
                                            flex items-center justify-center">
                                    🏪
                                </div>

                                <div>

                                    <p class="text-white font-medium">
                                        {{ $store->name }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        ID #{{ $store->id }}
                                    </p>

                                </div>

                            </div>

                            @if($store->is_active)

                                <span class="text-xs text-emerald-400">
                                    Aktif
                                </span>

                            @else

                                <span class="text-xs text-gray-500">
                                    Tidak aktif
                                </span>

                            @endif

                        </div>

                    @endforeach

                </div>

            @else

                <p class="text-gray-500">
                    Tidak ada toko
                </p>

            @endif

        </div>

    </div>

</div>


{{-- INFORMASI LANGGANAN --}}
<div class="bg-gray-800/80 border border-white/10
            rounded-2xl p-5 shadow-xl">

    <h2 class="text-lg font-semibold text-white mb-4">
        Informasi Langganan
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

        {{-- PAKET --}}
        <div>

            <p class="text-xs text-gray-500 mb-1">
                Paket
            </p>

            @if($subscription->plan)

                <span class="inline-flex px-2.5 py-1 rounded-lg
                             bg-indigo-500/10
                             text-indigo-400
                             text-xs font-medium">
                    {{ $subscription->plan->name }}
                </span>

            @else

                <p class="text-gray-500">
                    Tidak ada paket
                </p>

            @endif

        </div>


        {{-- STATUS --}}
        <div>

            <p class="text-xs text-gray-500 mb-1">
                Status
            </p>

            @if($subscription->status === 'active')

                <span class="inline-flex px-2.5 py-1 rounded-lg
                             bg-emerald-500/10
                             text-emerald-400
                             text-xs font-medium">
                    Aktif
                </span>

            @elseif($subscription->status === 'pending')

                <span class="inline-flex px-2.5 py-1 rounded-lg
                             bg-yellow-500/10
                             text-yellow-400
                             text-xs font-medium">
                    Menunggu
                </span>

            @elseif($subscription->status === 'expired')

                <span class="inline-flex px-2.5 py-1 rounded-lg
                             bg-red-500/10
                             text-red-400
                             text-xs font-medium">
                    Berakhir
                </span>

            @else

                <span class="inline-flex px-2.5 py-1 rounded-lg
                             bg-gray-500/10
                             text-gray-400
                             text-xs font-medium">
                    {{ ucfirst($subscription->status ?? 'Tidak diketahui') }}
                </span>

            @endif

        </div>


        {{-- ID SUBSCRIPTION --}}
        <div>

            <p class="text-xs text-gray-500 mb-1">
                ID Langganan
            </p>

            <p class="text-gray-300">
                #{{ $subscription->id }}
            </p>

        </div>


        {{-- MULAI --}}
        <div>

            <p class="text-xs text-gray-500 mb-1">
                Mulai Langganan
            </p>

            <p class="text-gray-300">
                {{ $subscription->starts_at?->format('d M Y') ?? '—' }}
            </p>

        </div>


        {{-- BERAKHIR --}}
        <div>

            <p class="text-xs text-gray-500 mb-1">
                Berakhir
            </p>

            <p class="text-gray-300">
                {{ $subscription->ends_at?->format('d M Y') ?? '—' }}
            </p>

        </div>


        {{-- DIBUAT --}}
        <div>

            <p class="text-xs text-gray-500 mb-1">
                Dibuat
            </p>

            <p class="text-gray-300">
                {{ $subscription->created_at?->format('d M Y H:i') ?? '—' }}
            </p>

        </div>

    </div>

</div>

</div>@endsection