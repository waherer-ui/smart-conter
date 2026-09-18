@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">
            Detail Pembayaran
        </h1>

        <p class="text-sm text-gray-400 mt-1">
            Informasi lengkap transaksi pembayaran paket.
        </p>
    </div>


    {{-- CARD --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl overflow-hidden shadow-xl">

        {{-- PLAN --}}
        <div class="p-5 border-b border-gray-700">

            <div class="flex items-center justify-between gap-3">

                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">
                        Paket
                    </p>

                    <h2 class="text-xl font-bold text-white mt-1">
                        {{ strtoupper($payment->plan->name) }}
                    </h2>
                </div>


                {{-- STATUS --}}
                @if($payment->status === 'paid')

                    <span class="px-3 py-1.5 rounded-full
                                 bg-emerald-500/10
                                 text-emerald-400
                                 text-xs font-semibold">
                        LUNAS
                    </span>

                @elseif($payment->status === 'pending')

                    <span class="px-3 py-1.5 rounded-full
                                 bg-yellow-500/10
                                 text-yellow-400
                                 text-xs font-semibold">
                        MENUNGGU
                    </span>

                @elseif($payment->status === 'failed')

                    <span class="px-3 py-1.5 rounded-full
                                 bg-red-500/10
                                 text-red-400
                                 text-xs font-semibold">
                        GAGAL
                    </span>

                @elseif($payment->status === 'expired')

                    <span class="px-3 py-1.5 rounded-full
                                 bg-gray-500/10
                                 text-gray-400
                                 text-xs font-semibold">
                        KADALUARSA
                    </span>

                @else

                    <span class="px-3 py-1.5 rounded-full
                                 bg-gray-500/10
                                 text-gray-400
                                 text-xs font-semibold">
                        {{ strtoupper($payment->status) }}
                    </span>

                @endif

            </div>

        </div>


{{-- DETAIL --}}
<div class="p-5 space-y-4">

    @php
        $durationMonths = (int) ($payment->duration_months ?? 1);

        $durationDiscounts = [
            1 => 0,
            3 => 5,
            6 => 7,
            12 => 12,
        ];

        $durationDiscountPercent =
            $durationDiscounts[$durationMonths] ?? 0;

        $normalAmount =
            $payment->plan->price * $durationMonths;

        $durationDiscountAmount = round(
            $normalAmount *
            ($durationDiscountPercent / 100)
        );
    @endphp


    {{-- DURASI --}}
    <div class="flex items-center justify-between gap-4">

        <span class="text-sm text-gray-400">
            Durasi Paket
        </span>

        <span class="text-sm font-medium text-white">
            @if($durationMonths === 12)
                1 Tahun
            @else
                {{ $durationMonths }} Bulan
            @endif
        </span>

    </div>


    {{-- HARGA NORMAL --}}
    <div class="flex items-center justify-between gap-4">

        <span class="text-sm text-gray-400">
            Harga Normal
        </span>

        <span class="text-sm text-gray-300">
            Rp {{ number_format($normalAmount, 0, ',', '.') }}
        </span>

    </div>


    {{-- DISKON DURASI --}}
    @if($durationDiscountPercent > 0)

        <div class="flex items-center justify-between gap-4">

            <span class="text-sm text-gray-400">
                Diskon Durasi {{ $durationDiscountPercent }}%
            </span>

            <span class="text-sm text-emerald-400">
                - Rp {{ number_format($durationDiscountAmount, 0, ',', '.') }}
            </span>

        </div>

    @endif


    {{-- REFERRAL --}}
    @if(
        !empty($payment->referral_code) &&
        (int) $payment->referral_discount_percent > 0
    )

        <div class="flex items-center justify-between gap-4">

            <span class="text-sm text-gray-400">
                Referral
            </span>

            <span class="text-sm font-medium text-white">
                {{ $payment->referral_code }}
            </span>

        </div>


        <div class="flex items-center justify-between gap-4">

            <span class="text-sm text-gray-400">
                Diskon Referral
                {{ $payment->referral_discount_percent }}%
            </span>

            <span class="text-sm text-emerald-400">
                - Rp {{ number_format($payment->referral_discount_amount, 0, ',', '.') }}
            </span>

        </div>

    @endif


    {{-- TOTAL --}}
    <div class="pt-4 border-t border-gray-700">

        <div class="flex items-center justify-between gap-4">

            <span class="text-sm font-semibold text-gray-300">
                Total Pembayaran
            </span>

            <span class="text-xl font-bold text-emerald-400">
                Rp {{ number_format($payment->amount, 0, ',', '.') }}
            </span>

        </div>

    </div>


    {{-- METODE --}}
    <div class="flex items-center justify-between gap-4">

        <span class="text-sm text-gray-400">
            Metode Pembayaran
        </span>

        <span class="text-sm font-medium text-white">
            {{ strtoupper($payment->payment_method ?? '-') }}
        </span>

    </div>


    {{-- REFERENCE --}}
    <div class="flex items-center justify-between gap-4">

        <span class="text-sm text-gray-400">
            Referensi
        </span>

        <span class="text-xs sm:text-sm font-mono text-gray-300 text-right break-all">
            {{ $payment->reference }}
        </span>

    </div>


    {{-- DIBUAT --}}
    <div class="flex items-center justify-between gap-4">

        <span class="text-sm text-gray-400">
            Dibuat
        </span>

        <span class="text-sm text-gray-300">
            {{ $payment->created_at->format('d M Y H:i') }}
        </span>

    </div>


    {{-- DIBAYAR --}}
    @if($payment->paid_at)

        <div class="flex items-center justify-between gap-4">

            <span class="text-sm text-gray-400">
                Dibayar
            </span>

            <span class="text-sm text-emerald-400">
                {{ $payment->paid_at->format('d M Y H:i') }}
            </span>

        </div>

    @endif

</div>


        {{-- ACTION --}}
        <div class="p-5 border-t border-gray-700 space-y-3">

            @if($payment->status === 'pending')

                <a href="{{ route('paket.payment.pending', $payment) }}"
                   class="block w-full text-center
                          bg-indigo-600 hover:bg-indigo-500
                          text-white font-semibold
                          py-3 rounded-xl transition">

                    Lanjutkan Pembayaran

                </a>

            @endif


            <a href="{{ route('paket.payment.history') }}"
               class="block w-full text-center
                      bg-gray-700 hover:bg-gray-600
                      text-white font-semibold
                      py-3 rounded-xl transition">

                ← Kembali ke Riwayat

            </a>

        </div>

    </div>

</div>

@endsection