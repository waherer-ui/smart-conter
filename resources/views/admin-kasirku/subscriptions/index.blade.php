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
            Pantau seluruh langganan pemilik akun KasirKU.
        </p>
    </div>

    <a
        href="{{ route('admin-kasirku.dashboard') }}"
        class="inline-flex items-center justify-center gap-2
               px-4 py-2 rounded-xl
               bg-gray-700 hover:bg-gray-600
               text-white text-sm font-medium transition"
    >
        ← Dashboard
    </a>

</div>


{{-- TABEL LANGGANAN --}}
<div class="bg-gray-800/80 border border-white/10
            rounded-2xl shadow-xl overflow-hidden">

    <div class="overflow-x-auto">

        <table class="w-full text-sm text-left">

            <thead class="bg-gray-900/70 border-b border-white/10">

                <tr>

                    <th class="px-5 py-4 text-gray-400 font-medium">
                        Pemilik
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium">
                        Toko
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium">
                        Paket
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium">
                        Mulai
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium">
                        Berakhir
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium">
                        Status
                    </th>

                    <th class="px-5 py-4 text-gray-400 font-medium">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-white/5">

                @forelse($subscriptions as $subscription)

                    <tr class="hover:bg-white/5 transition">

                        {{-- PEMILIK --}}
                        <td class="px-5 py-4">

                            @if($subscription->owner)

                                <p class="text-gray-200 font-medium">
                                    {{ $subscription->owner->name }}
                                </p>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $subscription->owner->email }}
                                </p>

                            @else

                                <span class="text-gray-500">
                                    Tidak ditemukan
                                </span>

                            @endif

                        </td>


                        {{-- TOKO --}}
                        <td class="px-5 py-4">

                            @if($subscription->owner?->stores?->count())

                                <div class="space-y-2">

                                    @foreach($subscription->owner->stores as $store)

                                        <div class="flex items-center gap-2">

                                            <span class="text-blue-400">
                                                🏪
                                            </span>

                                            <div>

                                                <p class="text-white text-sm">
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
                        <td class="px-5 py-4">

                            @if($subscription->plan)

                                <span class="inline-flex px-2.5 py-1 rounded-lg
                                             bg-indigo-500/10
                                             text-indigo-400
                                             text-xs font-medium">
                                    {{ $subscription->plan->name }}
                                </span>

                            @else

                                <span class="text-gray-500">
                                    Tidak ada paket
                                </span>

                            @endif

                        </td>


                        {{-- MULAI --}}
                        <td class="px-5 py-4 text-gray-400 whitespace-nowrap">

                            {{ $subscription->starts_at?->format('d M Y') ?? '—' }}

                        </td>


                        {{-- BERAKHIR --}}
                        <td class="px-5 py-4 text-gray-400 whitespace-nowrap">

                            {{ $subscription->ends_at?->format('d M Y') ?? '—' }}

                        </td>


                        {{-- STATUS --}}
                        <td class="px-5 py-4">

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

                        </td>


                        {{-- AKSI --}}
                        <td class="px-5 py-4">

                            <div class="flex items-center gap-2">

                                {{-- DETAIL --}}
                                <a
                                    href="{{ route('admin-kasirku.subscriptions.show', $subscription) }}"
                                    class="inline-flex items-center gap-2
                                           px-3 py-1.5 rounded-lg
                                           bg-blue-500/10
                                           hover:bg-blue-500/20
                                           text-blue-400
                                           text-xs font-medium
                                           transition"
                                >
                                    👁 Detail
                                </a>

                                {{-- EDIT --}}
                                <a
                                    href="{{ route('admin-kasirku.subscriptions.edit', $subscription) }}"
                                    class="inline-flex items-center gap-2
                                           px-3 py-1.5 rounded-lg
                                           bg-emerald-500/10
                                           hover:bg-emerald-500/20
                                           text-emerald-400
                                           text-xs font-medium
                                           transition"
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