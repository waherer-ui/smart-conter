@extends('layouts.app')

@section('title', 'Detail Pelanggan')
@section('header', '👥')

@section('content')

<div class="max-w-xl mx-auto space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center gap-3">

        <a
            href="{{ route('pelanggan.index') }}"
            class="w-9 h-9
                   flex items-center justify-center
                   bg-gray-800
                   border border-white/10
                   rounded-xl
                   text-gray-300
                   hover:text-white
                   transition"
        >
            ←
        </a>

        <div>
            <h2 class="text-lg font-bold text-white">
                Detail Pelanggan
            </h2>

            <p class="text-xs text-gray-400">
                Informasi pelanggan
            </p>
        </div>

    </div>

    {{-- PROFIL PELANGGAN --}}
    <div class="bg-gray-800/80
                border border-white/10
                rounded-2xl
                p-5">

        <div class="flex items-center gap-4">

            <div class="w-12 h-12
                        rounded-full
                        bg-emerald-500/10
                        border border-emerald-500/20
                        flex items-center justify-center
                        text-xl">
                👤
            </div>

            <div class="min-w-0">

                <h3 class="text-base font-bold text-white truncate">
                    {{ $customer->name }}
                </h3>

                @if($customer->phone)

                    <p class="text-xs text-gray-400 mt-1">
                        📱 {{ $customer->phone }}
                    </p>

                @else

                    <p class="text-xs text-gray-500 mt-1">
                        Nomor HP belum ditambahkan
                    </p>

                @endif

            </div>

        </div>

    </div>

    {{-- INFORMASI --}}
    <div class="bg-gray-800/80
                border border-white/10
                rounded-2xl
                overflow-hidden">

        <div class="px-4 py-3 border-b border-white/5">

            <div class="text-[11px] text-gray-500 uppercase">
                Nama
            </div>

            <div class="text-sm text-white mt-1">
                {{ $customer->name }}
            </div>

        </div>

        <div class="px-4 py-3 border-b border-white/5">

            <div class="text-[11px] text-gray-500 uppercase">
                Nomor HP
            </div>

            <div class="text-sm text-white mt-1">
                {{ $customer->phone ?: '-' }}
            </div>

        </div>

        <div class="px-4 py-3">

            <div class="text-[11px] text-gray-500 uppercase">
                Alamat
            </div>

            <div class="text-sm text-white mt-1 whitespace-pre-line">
                {{ $customer->address ?: '-' }}
            </div>

        </div>

    </div>

{{-- CATATAN UTANG --}}
@if($store->hasFeature('debt'))
<div class="bg-gray-800/80
            border border-white/10
            rounded-2xl
            p-5">

    <div class="flex items-center justify-between gap-3">

        <div>
            <h3 class="text-sm font-semibold text-white">
                Catatan Utang
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                Riwayat utang pelanggan akan muncul di sini.
            </p>
        </div>

        <span class="text-xl">
            💰
        </span>

    </div>


    {{-- TOTAL UTANG --}}
    <div class="mt-4
                bg-gray-900/70
                border border-white/5
                rounded-xl
                px-4 py-4
                text-center">

        <div class="text-xs text-gray-500">
            Total Utang
        </div>

        <div class="text-lg font-bold text-white mt-1">
            Rp {{ number_format($totalDebt, 0, ',', '.') }}
        </div>

    </div>


    {{-- DAFTAR UTANG --}}
    @if($customer->debts->count())

        <div class="mt-4 space-y-2">

            @foreach($customer->debts as $debt)

                <div class="bg-gray-900/70
            border border-white/5
            rounded-xl
            px-4 py-3">

    <div class="flex items-start justify-between gap-3">

        {{-- INFORMASI UTANG --}}
        <div class="min-w-0">

            {{-- NOMINAL AWAL --}}
            <div class="text-sm font-semibold text-white">
                Rp {{ number_format($debt->amount, 0, ',', '.') }}
            </div>

            @if($debt->description)

                <div class="text-xs text-gray-400 mt-1">
                    {{ $debt->description }}
                </div>

            @endif

            {{-- PEMBAYARAN --}}
            @if($debt->paid_amount > 0)

                <div class="text-[11px] text-emerald-400 mt-2">
                    Terbayar:
                    Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}
                </div>

            @endif

            {{-- SISA --}}
            @if($debt->remaining_amount > 0)

                <div class="text-[11px] text-red-400 mt-1">
                    Sisa:
                    Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                </div>

            @endif

            {{-- TANGGAL --}}
            <div class="text-[11px] text-gray-500 mt-2">

                Utang:
                {{ $debt->debt_date->format('d/m/Y') }}

                @if($debt->due_date)

                    • Jatuh tempo:
                    {{ $debt->due_date->format('d/m/Y') }}

                @endif

            </div>

            {{-- RIWAYAT CICILAN --}}
@if($debt->payments->count())

    <div class="mt-3 pt-3 border-t border-white/5">

        <div class="text-[10px]
                    text-gray-500
                    uppercase
                    font-semibold
                    mb-2">
            Riwayat Cicilan
        </div>

        <div class="space-y-2">

            @foreach($debt->payments as $payment)

                <div class="bg-gray-800/70
                            border border-white/5
                            rounded-lg
                            px-3 py-2">

                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">

                            <div class="text-xs
                                        font-semibold
                                        text-emerald-400">

                                Rp {{ number_format($payment->amount, 0, ',', '.') }}

                            </div>

                            <div class="text-[10px] text-gray-500 mt-1">

                                {{ $payment->payment_date->format('d/m/Y') }}

                                @if($payment->user)
                                    • {{ $payment->user->name }}
                                @endif

                            </div>

                            @if($payment->note)

                                <div class="text-[11px]
                                            text-gray-400
                                            mt-1">

                                    {{ $payment->note }}

                                </div>

                            @endif

                        </div>

                        <span class="text-[10px]
                                     text-emerald-400
                                     shrink-0">
                            ✓ Dibayar
                        </span>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

@endif

        </div>

        {{-- STATUS + BAYAR --}}
        <div class="text-right shrink-0">

            @if($debt->status === 'paid')

                <span class="text-[10px]
                             px-2 py-1
                             rounded-lg
                             bg-emerald-500/10
                             text-emerald-400">
                    Lunas
                </span>

            @else

                <span class="text-[10px]
                             px-2 py-1
                             rounded-lg
                             bg-red-500/10
                             text-red-400">
                    Belum Lunas
                </span>

                <a
                    href="{{ route('pelanggan.debt.payment.create', $debt->id) }}"
                    class="block mt-2
                           text-[10px]
                           bg-emerald-500/10
                           hover:bg-emerald-500/20
                           text-emerald-400
                           px-2.5 py-1.5
                           rounded-lg
                           font-semibold
                           transition"
                >
                    💵 Bayar
                </a>

            @endif

        </div>

    </div>

</div>

            @endforeach

        </div>

    @endif


    {{-- TAMBAH UTANG --}}
    <a
        href="{{ route('pelanggan.debt.create', $customer->id) }}"
        class="block mt-3
               text-center
               bg-emerald-500
               hover:bg-emerald-400
               text-gray-950
               px-4 py-3
               rounded-xl
               text-xs
               font-semibold
               transition"
    >
        + Tambah Utang
    </a>

</div>
@else

<div class="bg-gray-800/80
            border border-white/10
            rounded-2xl
            p-5">

    <div class="text-center">

        <div class="text-3xl mb-3">
            🔒
        </div>

        <h3 class="text-sm font-semibold text-white">
            Catatan Utang
        </h3>

        <p class="text-xs text-gray-500 mt-2 leading-relaxed">
            Fitur Catatan Utang tersedia pada paket Pro dan Premium.
        </p>

        <a
            href="{{ route('paket') }}"
            class="inline-block mt-4
                   bg-emerald-500
                   hover:bg-emerald-400
                   text-gray-950
                   px-4 py-2.5
                   rounded-xl
                   text-xs
                   font-semibold
                   transition"
        >
            🚀 Lihat Paket
        </a>

    </div>

</div>

@endif

@endsection