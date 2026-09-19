@extends('layouts.app')

@section('title', 'Pengguna KasirKU')
@section('header', 'Pengguna')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <div>
            <h1 class="text-2xl font-bold text-white">
                Pengguna KasirKU
            </h1>

            <p class="text-sm text-gray-400 mt-1">
                Pantau owner, toko, dan staf yang terhubung dalam setiap bisnis.
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


    {{-- RINGKASAN --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4">

            <p class="text-xs text-gray-500">
                Owner
            </p>

            <p class="mt-1 text-2xl font-bold text-white">
                {{ $owners->total() }}
            </p>

        </div>


        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4">

            <p class="text-xs text-gray-500">
                Toko
            </p>

            <p class="mt-1 text-2xl font-bold text-white">
                {{ $owners->sum('owned_stores_count') }}
            </p>

        </div>


        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-4
                    col-span-2 sm:col-span-1">

            <p class="text-xs text-gray-500">
                Platform
            </p>

            <p class="mt-1 text-lg font-bold text-emerald-400">
                KasirKU SaaS
            </p>

        </div>

    </div>


    {{-- DAFTAR OWNER --}}
    <div class="space-y-4">

        @forelse($owners as $owner)

            @php
                $subscription = $owner->subscription;
                $plan = $subscription?->plan;
                $isSubscriptionActive = $subscription?->isActive();

                $totalStaff = $owner->stores
                    ->sum(function ($store) use ($owner) {
                        return $store->users
                            ->where('id', '!=', $owner->id)
                            ->count();
                    });
            @endphp


            {{-- OWNER CARD --}}
{{-- OWNER CARD --}}
<a
    href="{{ route('admin-kasirku.users.show', $owner) }}"
    class="block bg-gray-800/80
           border border-white/10
           rounded-2xl
           shadow-xl
           overflow-hidden
           hover:border-emerald-500/30
           hover:bg-gray-800
           transition"
>


                {{-- OWNER HEADER --}}
                <div class="p-5 border-b border-white/10">

                    <div class="flex flex-col lg:flex-row
                                lg:items-center
                                lg:justify-between
                                gap-4">


                        {{-- IDENTITAS OWNER --}}
                        <div class="flex items-center gap-3">

                            <div class="w-11 h-11 rounded-full
                                        bg-emerald-500/10
                                        border border-emerald-500/20
                                        flex items-center justify-center
                                        text-emerald-400
                                        font-bold
                                        text-lg
                                        shrink-0">

                                {{ strtoupper(substr($owner->name, 0, 1)) }}

                            </div>


                            <div>

                                <div class="flex flex-wrap items-center gap-2">

                                    <h2 class="text-lg font-bold text-white">
                                        {{ $owner->name }}
                                    </h2>

                                    <span class="inline-flex px-2 py-0.5
                                                 rounded-md
                                                 bg-blue-500/10
                                                 text-blue-400
                                                 text-[11px]
                                                 font-medium">

                                        OWNER

                                    </span>

                                </div>

                                <p class="text-sm text-gray-400 mt-0.5">
                                    {{ $owner->email }}
                                </p>

                            </div>

                        </div>


                        {{-- PAKET --}}
                        <div class="flex flex-wrap items-center gap-2">

                            @if($plan)

                                <span class="inline-flex items-center gap-1.5
                                             px-3 py-1.5
                                             rounded-xl
                                             bg-indigo-500/10
                                             text-indigo-400
                                             text-xs
                                             font-medium">

                                    📦 {{ $plan->name }}

                                </span>


                                @if($isSubscriptionActive)

                                    <span class="inline-flex items-center gap-1.5
                                                 px-3 py-1.5
                                                 rounded-xl
                                                 bg-emerald-500/10
                                                 text-emerald-400
                                                 text-xs
                                                 font-medium">

                                        <span class="w-1.5 h-1.5
                                                     rounded-full
                                                     bg-emerald-400"></span>

                                        Aktif

                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1.5
                                                 px-3 py-1.5
                                                 rounded-xl
                                                 bg-red-500/10
                                                 text-red-400
                                                 text-xs
                                                 font-medium">

                                        Tidak Aktif

                                    </span>

                                @endif

                            @else

                                <span class="inline-flex items-center gap-1.5
                                             px-3 py-1.5
                                             rounded-xl
                                             bg-gray-700
                                             text-gray-400
                                             text-xs
                                             font-medium">

                                    📦 Free

                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- STAT OWNER --}}
                    <div class="flex flex-wrap gap-x-5 gap-y-2 mt-4
                                text-xs text-gray-400">

                        <span>
                            🏪 {{ $owner->stores_count }} Toko
                        </span>

                        <span>
                            👥 {{ $totalStaff }} Staf
                        </span>

                        <span>
                            📅 Terdaftar
                            {{ $owner->created_at?->format('d M Y') }}
                        </span>

                    </div>

                </div>


                {{-- DAFTAR TOKO --}}
                <div class="p-5 space-y-4">

                    @forelse($owner->stores as $store)

                        <div class="rounded-2xl
                                    bg-gray-900/60
                                    border border-white/10
                                    overflow-hidden">


                            {{-- STORE HEADER --}}
                            <div class="px-4 py-3
                                        border-b border-white/10
                                        flex flex-col sm:flex-row
                                        sm:items-center
                                        sm:justify-between
                                        gap-2">

                                <div class="flex items-center gap-2">

                                    <span class="text-lg">
                                        🏪
                                    </span>

                                    <div>

                                        <p class="text-white font-semibold">
                                            {{ $store->name }}
                                        </p>

                                        @if($store->address)

                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $store->address }}
                                            </p>

                                        @endif

                                    </div>

                                </div>


                                <span class="text-xs text-gray-500">

                                    {{ $store->users->count() }} pengguna

                                </span>

                            </div>


                            {{-- OWNER + STAFF --}}
                            <div class="p-4">

                                <div class="grid grid-cols-1
                                            sm:grid-cols-2
                                            lg:grid-cols-3
                                            gap-3">


                                    {{-- OWNER --}}
                                    <div class="flex items-center gap-3
                                                p-3 rounded-xl
                                                bg-blue-500/5
                                                border border-blue-500/10">

                                        <div class="w-9 h-9 rounded-full
                                                    bg-blue-500/10
                                                    flex items-center justify-center
                                                    text-blue-400
                                                    font-semibold
                                                    shrink-0">

                                            {{ strtoupper(substr($owner->name, 0, 1)) }}

                                        </div>


                                        <div class="min-w-0">

                                            <p class="text-sm text-white font-medium truncate">
                                                {{ $owner->name }}
                                            </p>

                                            <span class="text-[11px] text-blue-400">
                                                Owner
                                            </span>

                                        </div>

                                    </div>


                                    {{-- STAF / KASIR --}}
                                    @foreach(
                                        $store->users->where('id', '!=', $owner->id)
                                        as $staff
                                    )

                                        <div class="flex items-center gap-3
                                                    p-3 rounded-xl
                                                    bg-gray-800
                                                    border border-white/5">

                                            <div class="w-9 h-9 rounded-full
                                                        bg-gray-700
                                                        flex items-center justify-center
                                                        text-gray-300
                                                        font-semibold
                                                        shrink-0">

                                                {{ strtoupper(substr($staff->name, 0, 1)) }}

                                            </div>


                                            <div class="min-w-0">

                                                <p class="text-sm text-white font-medium truncate">
                                                    {{ $staff->name }}
                                                </p>

                                                <span class="text-[11px] text-gray-500">
                                                    {{ ucfirst($staff->pivot->role ?? $staff->role) }}
                                                </span>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="rounded-xl
                                    border border-dashed border-white/10
                                    p-6 text-center">

                            <p class="text-sm text-gray-500">
                                Owner ini belum memiliki toko.
                            </p>

                        </div>

                    @endforelse

                </div>

            </a>

        @empty

            <div class="bg-gray-800/80
                        border border-white/10
                        rounded-2xl
                        p-10
                        text-center">

                <div class="text-3xl mb-3">
                    👤
                </div>

                <p class="text-gray-400">
                    Belum ada owner yang memiliki toko.
                </p>

            </div>

        @endforelse

    </div>


    {{-- PAGINATION --}}
    @if($owners->hasPages())

        <div class="bg-gray-800/80
                    border border-white/10
                    rounded-2xl
                    px-5 py-4">

            {{ $owners->links() }}

        </div>

    @endif

</div>

@endsection