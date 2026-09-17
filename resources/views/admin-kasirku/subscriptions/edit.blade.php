@extends('layouts.app')

@section('title', 'Edit Langganan')
@section('header', 'Langganan')

@section('content')

<div class="space-y-6">{{-- HEADER --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

    <div>
        <h1 class="text-2xl font-bold text-white">
            Edit Langganan
        </h1>

        <p class="text-sm text-gray-400 mt-1">
            Kelola paket dan periode langganan pemilik akun KasirKU.
        </p>
    </div>

    <a
        href="{{ route('admin-kasirku.subscriptions.show', $subscription) }}"
        class="inline-flex items-center justify-center gap-2
               px-4 py-2 rounded-xl
               bg-gray-700 hover:bg-gray-600
               text-white text-sm font-medium transition"
    >
        ← Kembali
    </a>

</div>


{{-- ERROR VALIDASI --}}
@if ($errors->any())

    <div class="rounded-xl border border-red-500/20
                bg-red-500/10 p-4">

        <div class="flex items-start gap-3">

            <div class="text-red-400 text-lg">
                ⚠️
            </div>

            <div>

                <p class="text-sm font-semibold text-red-300">
                    Periksa kembali data yang dimasukkan.
                </p>

                <ul class="mt-2 space-y-1 text-xs text-red-300/90">

                    @foreach ($errors->all() as $error)

                        <li>
                            • {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    </div>

@endif


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

            <p class="text-white font-medium">
                {{ $subscription->owner?->name ?? '—' }}
            </p>

            @if($subscription->owner?->email)

                <p class="text-xs text-gray-500 mt-1">
                    {{ $subscription->owner->email }}
                </p>

            @endif

        </div>


        {{-- TOKO --}}
        <div>

            <p class="text-xs text-gray-500 mb-2">
                Toko Milik Owner
            </p>

            @if($subscription->owner?->stores?->count())

                <div class="space-y-2">

                    @foreach($subscription->owner->stores as $store)

                        <div class="flex items-center gap-2">

                            <span class="text-blue-400">
                                🏪
                            </span>

                            <div>

                                <p class="text-gray-200 text-sm">
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

        </div>

    </div>

</div>


{{-- FORM LANGGANAN --}}
<div class="bg-gray-800/80 border border-white/10
            rounded-2xl p-5 shadow-xl">

    <h2 class="text-lg font-semibold text-white mb-5">
        Pengaturan Langganan Owner
    </h2>

    <form
        action="{{ route('admin-kasirku.subscriptions.update', $subscription->id) }}"
        method="POST"
        class="space-y-5"
    >

        @csrf
        @method('PUT')


        {{-- PAKET --}}
        <div>

            <label class="block text-sm text-gray-300 mb-2">
                Paket
            </label>

            <select
                name="plan_id"
                class="w-full rounded-xl
                       bg-gray-900
                       border border-white/10
                       text-white
                       px-4 py-3
                       focus:outline-none
                       focus:border-indigo-500"
            >

                @foreach($plans as $plan)

                    <option
                        value="{{ $plan->id }}"
                        @selected($subscription->plan_id == $plan->id)
                    >
                        {{ $plan->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- STATUS --}}
        <div>

            <label class="block text-sm text-gray-300 mb-2">
                Status
            </label>

            <select
                name="status"
                class="w-full rounded-xl
                       bg-gray-900
                       border border-white/10
                       text-white
                       px-4 py-3
                       focus:outline-none
                       focus:border-indigo-500"
            >

                <option
                    value="active"
                    @selected($subscription->status === 'active')
                >
                    Aktif
                </option>

                <option
                    value="pending"
                    @selected($subscription->status === 'pending')
                >
                    Menunggu
                </option>

                <option
                    value="expired"
                    @selected($subscription->status === 'expired')
                >
                    Berakhir
                </option>

                <option
                    value="cancelled"
                    @selected($subscription->status === 'cancelled')
                >
                    Dibatalkan
                </option>

            </select>

        </div>


        {{-- MULAI --}}
        <div>

            <label class="block text-sm text-gray-300 mb-2">
                Mulai Langganan
            </label>

            <input
                type="date"
                name="starts_at"
                value="{{ $subscription->starts_at?->format('Y-m-d') }}"
                class="w-full rounded-xl
                       bg-gray-900
                       border border-white/10
                       text-white
                       px-4 py-3
                       focus:outline-none
                       focus:border-indigo-500"
            >

        </div>


        {{-- BERAKHIR --}}
        <div>

            <label class="block text-sm text-gray-300 mb-2">
                Berakhir
            </label>

            <input
                type="date"
                name="ends_at"
                value="{{ $subscription->ends_at?->format('Y-m-d') }}"
                class="w-full rounded-xl
                       bg-gray-900
                       border border-white/10
                       text-white
                       px-4 py-3
                       focus:outline-none
                       focus:border-indigo-500"
            >

            <p class="text-xs text-gray-500 mt-2">
                Kosongkan jika langganan tidak memiliki tanggal berakhir.
            </p>

        </div>


        {{-- TOMBOL --}}
        <div class="flex flex-col sm:flex-row gap-3 pt-3">

            <button
                type="submit"
                class="inline-flex items-center justify-center
                       px-5 py-3 rounded-xl
                       bg-indigo-600
                       hover:bg-indigo-500
                       text-white text-sm font-medium
                       transition"
            >
                💾 Simpan Perubahan
            </button>

            <a
                href="{{ route('admin-kasirku.subscriptions.show', $subscription) }}"
                class="inline-flex items-center justify-center
                       px-5 py-3 rounded-xl
                       bg-gray-700
                       hover:bg-gray-600
                       text-white text-sm font-medium
                       transition"
            >
                Batal
            </a>

        </div>

    </form>

</div>

</div>@endsection