@extends('layouts.app')

@section('title', 'Langganan KasirKU')
@section('header', 'Langganan')

@section('content')

<div class="space-y-6">{{-- HEADER --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

    <div>
        <h1 class="text-2xl font-bold text-white">
            Langganan KasirKU
        </h1>

        <p class="text-sm text-gray-400 mt-1">
            Pantau paket dan status langganan seluruh pemilik akun KasirKU.
        </p>
    </div>

    <a
        href="{{ route('admin-kasirku.dashboard') }}"
        class="inline-flex items-center justify-center gap-2
               px-4 py-2 rounded-xl
               bg-gray-700 hover:bg-gray-600
               text-white text-sm font-medium
               transition"
    >
        ← Dashboard
    </a>

</div>


{{-- INFO --}}
<div class="bg-indigo-500/10 border border-indigo-500/20
            rounded-2xl p-4">

    <div class="flex items-start gap-3">

        <div class="w-9 h-9 shrink-0 rounded-xl
                    bg-indigo-500/10
                    flex items-center justify-center">
            📋
        </div>

        <div>
            <p class="text-sm font-medium text-indigo-300">
                Sistem Langganan Berbasis Pemilik
            </p>

            <p class="text-xs text-gray-400 mt-1 leading-relaxed">
                Satu pemilik akun dapat memiliki beberapa toko.
                Paket langganan berlaku untuk akun pemilik dan seluruh toko
                yang dikelolanya.
            </p>
        </div>

    </div>

</div>


{{-- TABEL LANGGANAN --}}
<div class="bg-gray-800/80 border border-white/10
            rounded-2xl shadow-xl overflow-hidden">

    <div class="px-5 py-4 border-b border-white/10">

        <div class="flex items-center justify-between gap-3">

            <div>
                <h2 class="text-base font-semibold text-white">
                    Daftar Langganan
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    {{ $subscriptions->total() }} langganan terdaftar
                </p>
            </div>

        </div>

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-sm text-left">

            <thead class="bg-gray-900/70 border-b border-white/10">

                <tr>

                    <th class="px-5 py-4 text-gray-400 font-medium whitespace-nowrap">
                        Pemilik
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium whitespace-nowrap">
                        Toko
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium whitespace-nowrap">
                        Paket
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium whitespace-nowrap">
                        Mulai
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium whitespace-nowrap">
                        Berakhir
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium whitespace-nowrap">
                        Status
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium whitespace-nowrap">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-white/5">

                @forelse($subscriptions as $subscription)

                    <tr class="hover:bg-white/5 transition">


                        {{-- PEMILIK --}}
                        <td class="px-5 py-4 align-top">

                            @if($subscription->owner)

                                <div class="flex items-center gap-3">

                                    <div class="w-9 h-9 shrink-0 rounded-full
                                                bg-emerald-600/20
                                                border border-emerald-500/20
                                                flex items-center justify-center
                                                text-emerald-400
                                                font-semibold">

                                        {{ strtoupper(substr($subscription->owner->name, 0, 1)) }}

                                    </div>

                                    <div class="min-w-0">

                                        <p class="text-gray-200 font-medium whitespace-nowrap">
                                            {{ $subscription->owner->name }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $subscription->owner->email }}
                                        </p>

                                    </div>

                                </div>

                            @else

                                <span class="text-gray-500">
                                    Pemilik tidak ditemukan
                                </span>

                            @endif

                        </td>


                        {{-- TOKO --}}
                        <td class="px-5 py-4 align-top">

                            @if($subscription->owner?->stores?->count())

                                <div class="space-y-2">

                                    @foreach($subscription->owner->stores as $store)

                                        <div class="flex items-start gap-2">

                                            <span class="text-blue-400 mt-0.5">
                                                🏪
                                            </span>

                                            <div class="min-w-0">

                                                <p class="text-gray-200 text-sm whitespace-nowrap">
                                                    {{ $store->name }}
                                                </p>

                                                <p class="text-xs text-gray-500">
                                                    ID #{{ $store->id }}
                                                </p>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            @else

                                <span class="text-gray-500">
                                    Tidak ada toko
                                </span>

                            @endif

                        </td>


                        {{-- PAKET --}}
                        <td class="px-5 py-4 align-top">

                            @if($subscription->plan)

                                @php
                                    $planSlug = strtolower($subscription->plan->slug ?? '');
                                @endphp

                                @if($planSlug === 'premium')

                                    <span class="inline-flex px-2.5 py-1 rounded-lg
                                                 bg-purple-500/10
                                                 text-purple-400
                                                 text-xs font-medium">
                                        {{ $subscription->plan->name }}
                                    </span>

                                @elseif($planSlug === 'pro')

                                    <span class="inline-flex px-2.5 py-1 rounded-lg
                                                 bg-indigo-500/10
                                                 text-indigo-400
                                                 text-xs font-medium">
                                        {{ $subscription->plan->name }}
                                    </span>

                                @elseif($planSlug === 'free')

                                    <span class="inline-flex px-2.5 py-1 rounded-lg
                                                 bg-gray-500/10
                                                 text-gray-300
                                                 text-xs font-medium">
                                        {{ $subscription->plan->name }}
                                    </span>

                                @else

                                    <span class="inline-flex px-2.5 py-1 rounded-lg
                                                 bg-gray-500/10
                                                 text-gray-300
                                                 text-xs font-medium">
                                        {{ $subscription->plan->name }}
                                    </span>

                                @endif

                            @else

                                <span class="text-gray-500">
                                    Tidak ada paket
                                </span>

                            @endif

                        </td>


                        {{-- MULAI --}}
                        <td class="px-5 py-4 text-gray-400 whitespace-nowrap align-top">

                            {{ $subscription->starts_at?->format('d M Y') ?? '—' }}

                        </td>


                        {{-- BERAKHIR --}}
                        <td class="px-5 py-4 text-gray-400 whitespace-nowrap align-top">

                            {{ $subscription->ends_at?->format('d M Y') ?? '—' }}

                        </td>


                        {{-- STATUS --}}
                        <td class="px-5 py-4 align-top">

                            @if($subscription->status === 'active' && $subscription->isActive())

                                <span class="inline-flex items-center gap-1.5
                                             px-2.5 py-1 rounded-lg
                                             bg-emerald-500/10
                                             text-emerald-400
                                             text-xs font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Aktif
                                </span>

                            @elseif($subscription->status === 'active' && !$subscription->isActive())

                                <span class="inline-flex items-center gap-1.5
                                             px-2.5 py-1 rounded-lg
                                             bg-red-500/10
                                             text-red-400
                                             text-xs font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                    Berakhir
                                </span>

                            @elseif($subscription->status === 'pending')

                                <span class="inline-flex items-center gap-1.5
                                             px-2.5 py-1 rounded-lg
                                             bg-yellow-500/10
                                             text-yellow-400
                                             text-xs font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                                    Menunggu
                                </span>

                            @elseif($subscription->status === 'expired')

                                <span class="inline-flex items-center gap-1.5
                                             px-2.5 py-1 rounded-lg
                                             bg-red-500/10
                                             text-red-400
                                             text-xs font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                    Berakhir
                                </span>

                            @elseif($subscription->status === 'cancelled')

                                <span class="inline-flex items-center gap-1.5
                                             px-2.5 py-1 rounded-lg
                                             bg-gray-500/10
                                             text-gray-400
                                             text-xs font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Dibatalkan
                                </span>

                            @else

                                <span class="inline-flex px-2.5 py-1 rounded-lg
                                             bg-gray-500/10
                                             text-gray-400
                                             text-xs font-medium">
                                    {{ ucfirst($subscription->status ?? 'Tidak diketahui') }}
                                </span>

                            @endif

                        </td>


                        {{-- AKSI --}}
                        <td class="px-5 py-4 align-top">

                            <div class="flex items-center gap-2">

                                <a
                                    href="{{ route('admin-kasirku.subscriptions.show', $subscription) }}"
                                    class="inline-flex items-center gap-2
                                           px-3 py-1.5 rounded-lg
                                           bg-blue-500/10
                                           hover:bg-blue-500/20
                                           text-blue-400
                                           text-xs font-medium
                                           transition whitespace-nowrap"
                                >
                                    👁 Detail
                                </a>

                                <a
                                    href="{{ route('admin-kasirku.subscriptions.edit', $subscription) }}"
                                    class="inline-flex items-center gap-2
                                           px-3 py-1.5 rounded-lg
                                           bg-emerald-500/10
                                           hover:bg-emerald-500/20
                                           text-emerald-400
                                           text-xs font-medium
                                           transition whitespace-nowrap"
                                >
                                    ✏️ Edit
                                </a>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="px-5 py-10 text-center text-gray-500"
                        >
                            Belum ada data langganan.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}
    @if($subscriptions->hasPages())

        <div class="px-5 py-4 border-t border-white/10">
            {{ $subscriptions->links() }}
        </div>

    @endif

</div>

</div>@endsection