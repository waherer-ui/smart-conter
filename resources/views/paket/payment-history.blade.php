@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')
@section('header', 'Riwayat Pembayaran')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-white">
            Riwayat Pembayaran
        </h1>

        <p class="mt-1 text-sm text-gray-400">
            Lihat seluruh transaksi pembayaran paket Anda.
        </p>
    </div>


    {{-- RIWAYAT --}}
    <div class="bg-gray-800/80
                border border-white/10
                rounded-2xl
                shadow-xl
                overflow-hidden">

@forelse($payments as $payment)

    <a
        href="{{ route('paket.payment.detail', $payment) }}"
        class="block group"
    >

        <div class="p-5
                    border-b border-white/10
                    last:border-b-0
                    hover:bg-white/[0.03]
                    transition">

            <div class="flex flex-col
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                        gap-4">

                {{-- INFO UTAMA --}}
                <div>

                    <div class="flex items-center gap-3">

                        <h2 class="text-base
                                   font-bold text-white
                                   group-hover:text-indigo-400
                                   transition">
                            {{ strtoupper($payment->plan->name) }}
                        </h2>

                        @if($payment->status === 'paid')

                            <span class="px-2 py-1
                                         rounded-full
                                         text-[10px]
                                         font-semibold
                                         bg-emerald-500/10
                                         text-emerald-400
                                         border border-emerald-500/20">
                                LUNAS
                            </span>

                        @elseif($payment->status === 'pending')

                            <span class="px-2 py-1
                                         rounded-full
                                         text-[10px]
                                         font-semibold
                                         bg-yellow-500/10
                                         text-yellow-400
                                         border border-yellow-500/20">
                                MENUNGGU
                            </span>

                        @elseif($payment->status === 'failed')

                            <span class="px-2 py-1
                                         rounded-full
                                         text-[10px]
                                         font-semibold
                                         bg-red-500/10
                                         text-red-400
                                         border border-red-500/20">
                                GAGAL
                            </span>

                        @else

                            <span class="px-2 py-1
                                         rounded-full
                                         text-[10px]
                                         font-semibold
                                         bg-gray-500/10
                                         text-gray-400
                                         border border-white/10">
                                {{ strtoupper($payment->status) }}
                            </span>

                        @endif

                    </div>


<p class="mt-1 text-sm text-gray-400">

    {{ strtoupper($payment->payment_method ?? '-') }}

    ·

    @if(($payment->duration_months ?? 1) == 12)
        1 Tahun
    @else
        {{ $payment->duration_months ?? 1 }} Bulan
    @endif

    ·

    {{ $payment->created_at->format('d M Y H:i') }}

</p>


                    <p class="mt-2 text-xs
                              font-mono text-gray-500">

                        {{ $payment->reference }}

                    </p>
                    
                    @if(
    !empty($payment->referral_code) &&
    (int) $payment->referral_discount_percent > 0
)

    <p class="mt-2 text-xs text-emerald-400">

        ✓ Referral {{ $payment->referral_code }}

        · Diskon {{ $payment->referral_discount_percent }}%

    </p>

@endif

                </div>


                {{-- NOMINAL --}}
                <div class="sm:text-right">

                    <p class="text-lg
                              font-bold text-emerald-400">

                        Rp {{ number_format($payment->amount, 0, ',', '.') }}

                    </p>


                    @if($payment->paid_at)

                        <p class="mt-1 text-xs
                                  text-gray-500">

                            Dibayar
                            {{ $payment->paid_at->format('d M Y H:i') }}

                        </p>

                    @endif

                </div>

            </div>

        </div>

    </a>

@empty

    <div class="p-10 text-center">

        <div class="text-4xl">
            🧾
        </div>

        <h2 class="mt-4
                   text-lg font-semibold
                   text-white">

            Belum Ada Pembayaran

        </h2>

        <p class="mt-1 text-sm
                  text-gray-500">

            Riwayat pembayaran paket Anda
            akan muncul di sini.

        </p>

    </div>

@endforelse

    </div>


    {{-- KEMBALI --}}
    <div class="text-center">

        <a
            href="{{ route('paket') }}"
            class="inline-flex items-center
                   px-4 py-2 rounded-xl
                   bg-gray-800
                   border border-white/10
                   text-sm text-gray-400
                   hover:text-white
                   hover:border-white/20
                   transition"
        >
            ← Kembali ke Paket
        </a>

    </div>

</div>

@endsection