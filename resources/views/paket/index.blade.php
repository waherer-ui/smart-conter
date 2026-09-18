@extends('layouts.app')

@section('title', 'Paket KasirKU')
@section('header', 'Paket KasirKU')

@section('content')

@php
    $currentPlan = $activeStore?->currentPlan();
@endphp

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-white">
            Paket KasirKU
        </h1>

        <p class="text-sm text-gray-400 mt-1">
            Pilih paket yang sesuai dengan kebutuhan toko Anda.
        </p>
    </div>

    {{-- =========================================================
     PAKET AKTIF OWNER
========================================================= --}}
@if($subscription && $subscription->isActive())

    @php
        $daysRemaining = $subscription->ends_at
    ? max(
        0,
        (int) ceil(
            now()->diffInSeconds(
                $subscription->ends_at
            ) / 86400
        )
    )
    : null;
    @endphp

    <div class="mb-6 rounded-2xl border border-emerald-500/20
                bg-gradient-to-br from-gray-800 to-gray-900
                p-5 shadow-lg">

        <div class="flex items-start justify-between gap-4">

            <div>
                <p class="text-xs font-semibold uppercase
                          tracking-wider text-emerald-400">
                    Paket Anda Saat Ini
                </p>

                <h2 class="mt-1 text-2xl font-bold text-white">
                    {{ strtoupper($subscription->plan->name) }}
                </h2>

                <div class="mt-2 inline-flex items-center gap-2
                            rounded-full bg-emerald-500/10
                            px-3 py-1 text-xs font-medium
                            text-emerald-400">

                    <span class="h-2 w-2 rounded-full
                                 bg-emerald-400"></span>

                    Aktif
                </div>
            </div>

            <div class="text-right">

                @if($subscription->ends_at)

                    <p class="text-xs text-gray-400">
                        Berlaku sampai
                    </p>

                    <p class="mt-1 font-semibold text-white">
                        {{ $subscription->ends_at
                            ->locale('id')
                            ->translatedFormat('d F Y') }}
                    </p>

                @else

                    <p class="text-sm font-semibold
                              text-emerald-400">
                        Tidak terbatas
                    </p>

                @endif

            </div>

        </div>

<div class="mt-5 grid grid-cols-3 gap-3">

    {{-- TOKO TERCAKUP --}}
    <div class="rounded-xl bg-gray-900/70 p-3">
        <p class="text-xs text-gray-500">
            Toko Tercakup
        </p>

        <p class="mt-1 font-semibold text-white">
            {{ $storeCount }} Toko
        </p>
    </div>

    {{-- DURASI PAKET --}}
    <div class="rounded-xl bg-gray-900/70 p-3">
        <p class="text-xs text-gray-500">
            Durasi Paket
        </p>

        <p class="mt-1 font-semibold text-white">
            {{ $subscription->duration_months }} Bulan
        </p>
    </div>

    {{-- MASA AKTIF --}}
    <div class="rounded-xl bg-gray-900/70 p-3">

        <p class="text-xs text-gray-500">
            Masa Aktif
        </p>

        <p class="mt-1 font-semibold text-white">

            @if($daysRemaining !== null)
                {{ $daysRemaining }} hari lagi
            @else
                Tidak terbatas
            @endif

        </p>

    </div>

</div>

        <div class="mt-4 flex flex-wrap gap-2">

            <a
                href="{{ route('paket.payment.history') }}"
                class="inline-flex items-center justify-center
                       rounded-xl bg-gray-700 px-4 py-2.5
                       text-sm font-semibold text-white
                       transition hover:bg-gray-600"
            >
                Riwayat Pembayaran
            </a>

        </div>

    </div>

@endif


    {{-- PAKET --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

       @foreach($plans as $plan)

    @php
        $isCurrentPlan = $currentPlan
            && $currentPlan->id === $plan->id;
    @endphp

    <div
        class="relative bg-gray-800/80 border rounded-2xl p-5 shadow-xl
        {{ $isCurrentPlan
            ? 'border-emerald-400/60 ring-1 ring-emerald-400/30'
            : 'border-white/10' }}"
    >
      @if($isCurrentPlan)
    <div class="absolute top-4 right-4">
        <span
            class="inline-flex items-center gap-1
                   px-2.5 py-1 rounded-full
                   bg-emerald-500/15
                   border border-emerald-400/20
                   text-emerald-400
                   text-xs font-semibold"
        >
            <span>●</span>
            Paket Anda
        </span>
    </div>
@endif

                {{-- NAMA --}}
                <h2 class="text-xl font-bold text-white">
                    {{ strtoupper($plan->name) }}
                </h2>


                {{-- HARGA --}}
                <div class="mt-3">

                    <span class="text-3xl font-bold text-emerald-400">
                        Rp {{ number_format($plan->price, 0, ',', '.') }}
                    </span>

                    @if($plan->price > 0)
                        <span class="text-sm text-gray-500">
                            / bulan
                        </span>
                    @endif

                </div>


                {{-- DESKRIPSI --}}
                @if($plan->description)

                    <p class="mt-3 text-sm text-gray-400">
                        {{ $plan->description }}
                    </p>

                @endif


                {{-- LIMIT --}}
                <div class="mt-5">

                    <p class="text-xs font-semibold
                              text-gray-500 uppercase mb-2">
                        Batas Penggunaan
                    </p>

                   @foreach($plan->limits as $limit)

                @php
                    $limitLabel = match ($limit->key) {
                        'history_days' => 'Riwayat data',
                        'max_customers' => 'Pelanggan tersimpan',
                        'max_products' => 'Jenis produk',
                        'max_staff' => 'Akun staff/kasir',
                        'max_stores' => 'Toko/cabang',
                        default => $limit->key,
                    };

                    $limitValue = match ($limit->key) {
                        'history_days' => $limit->value === null
                            ? 'Tanpa batas'
                            : $limit->value . ' hari',

                        'max_customers' => $limit->value === null
                            ? 'Tanpa batas'
                            : 'Hingga ' . number_format($limit->value, 0, ',', '.') . ' pelanggan',

                        'max_products' => $limit->value === null
                            ? 'Tanpa batas'
                            : 'Hingga ' . number_format($limit->value, 0, ',', '.') . ' jenis produk',

                        'max_staff' => $limit->value === null
                            ? 'Tanpa batas'
                            : number_format($limit->value, 0, ',', '.'). ' akun',

                        'max_stores' => $limit->value === null
                            ? 'Tanpa batas'
                            : 'Hingga ' . number_format($limit->value) . ' toko',

                        default => $limit->value === null
                            ? 'Tanpa batas'
                            : number_format($limit->value, 0, ',', '.'),
                    };
                @endphp

    <div class="flex items-center justify-between gap-3 py-2">
        <span class="text-sm text-gray-400">
            {{ $limitLabel }}
        </span>

        <span class="text-sm text-white font-medium text-right">
            {{ $limitValue }}
        </span>
    </div>

@endforeach

                </div>


                {{-- FITUR --}}
                <div class="mt-5">

                    <p class="text-xs font-semibold
                              text-gray-500 uppercase mb-2">
                        Fitur
                    </p>

                    <div class="space-y-2">

                        @foreach($plan->features as $feature)

                            <div class="flex items-start gap-2">

                                <span class="text-emerald-400">
                                    ✓
                                </span>

                                <span class="text-sm text-gray-300">
                                    {{ $feature->name }}
                                </span>

                            </div>

                        @endforeach

                    </div>

                </div>


                {{-- BUTTON --}}
@if($isCurrentPlan)

    <button
        type="button"
        disabled
        class="mt-6 w-full px-4 py-2.5 rounded-xl
               bg-gray-700
               text-gray-400
               font-semibold
               cursor-default"
    >
        ✓ Paket Saat Ini
    </button>

@else

    <form
        action="{{ route('paket.select', $plan->id) }}"
        method="POST"
        class="mt-6"
    >

        @csrf

        <button
            type="submit"
            class="w-full px-4 py-2.5 rounded-xl
                   bg-emerald-500
                   hover:bg-emerald-400
                   text-gray-950
                   font-semibold
                   transition"
        >
            Pilih Paket
        </button>

    </form>

@endif

            </div>

        @endforeach

    </div>


    {{-- RIWAYAT PEMBAYARAN --}}
    <div class="flex justify-center">

        <a
            href="{{ route('paket.payment.history') }}"
            class="inline-flex items-center gap-2
                   px-5 py-2.5
                   rounded-xl
                   bg-gray-800
                   border border-white/10
                   text-sm font-semibold
                   text-gray-300
                   hover:text-white
                   hover:border-white/20
                   hover:bg-gray-700
                   transition"
        >
            <span>🧾</span>
            Riwayat Pembayaran
        </a>

    </div>

</div>

@endsection