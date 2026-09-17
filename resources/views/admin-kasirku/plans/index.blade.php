@extends('layouts.app')

@section('title', 'Paket')
@section('header', '📦')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-white">
                    Paket KasirKU
                </h1>

                <p class="mt-1 text-sm text-gray-400">
                    Kelola paket, harga, fitur, dan batas penggunaan toko.
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
    </div>


    {{-- DAFTAR PAKET --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

        @forelse($plans as $plan)

            <div class="bg-gray-800/80 border border-white/10
                        rounded-2xl p-5 shadow-xl">

                {{-- NAMA + STATUS --}}
                <div class="flex items-start justify-between gap-3">

                    <div>
                        <h2 class="text-lg font-bold text-white">
                            {{ $plan->name }}
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ $plan->slug }}
                        </p>
                    </div>

                    @if($plan->is_active)
                        <span class="px-2.5 py-1 rounded-full
                                     bg-emerald-500/10
                                     text-emerald-400 text-xs font-medium">
                            Aktif
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full
                                     bg-gray-500/10
                                     text-gray-400 text-xs font-medium">
                            Nonaktif
                        </span>
                    @endif

                </div>


                {{-- HARGA --}}
                <div class="mt-5">

                    <p class="text-2xl font-bold text-white">
                        Rp {{ number_format($plan->price, 0, ',', '.') }}
                    </p>

                    <p class="text-xs text-gray-500 mt-1">
                        per periode langganan
                    </p>

                </div>


                {{-- DESKRIPSI --}}
                @if($plan->description)
                    <p class="mt-4 text-sm text-gray-400 line-clamp-2">
                        {{ $plan->description }}
                    </p>
                @endif


                {{-- RINGKASAN --}}
                <div class="grid grid-cols-2 gap-3 mt-5">

                    <div class="rounded-xl bg-gray-900/60 p-3">
                        <p class="text-xs text-gray-500">
                            Fitur
                        </p>

                        <p class="mt-1 text-lg font-semibold text-white">
                            {{ $plan->features->count() }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-gray-900/60 p-3">
                        <p class="text-xs text-gray-500">
                            Pemilik
                        </p>

                        <p class="mt-1 text-lg font-semibold text-white">
                            {{ $plan->subscriptions->count() }}
                        </p>
                    </div>

                </div>


                {{-- AKSI --}}
                <div class="mt-5 pt-4 border-t border-white/10">

                    <a
                        href="{{ route('admin-kasirku.plans.edit', $plan) }}"
                        class="inline-flex items-center justify-center gap-2
                               w-full px-4 py-2.5 rounded-xl
                               bg-blue-500/10
                               hover:bg-blue-500/20
                               text-blue-400
                               text-sm font-medium transition"
                    >
                        ✏️ Edit Paket
                    </a>

                </div>

            </div>

        @empty

            <div class="md:col-span-2 xl:col-span-3
                        bg-gray-800/80 border border-white/10
                        rounded-2xl p-8 text-center">

                <p class="text-gray-400">
                    Belum ada paket.
                </p>

            </div>

        @endforelse

    </div>

</div>

@endsection