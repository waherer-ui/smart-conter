@extends('layouts.app')

@section('title', 'Laporan Piutang')
@section('header', '💰')

@section('content')

<div class="space-y-4">

    {{-- HEADER --}}
    <div>
        <h1 class="text-lg font-semibold text-white">
            Laporan Piutang
        </h1>

        <p class="text-xs text-gray-500 mt-1">
            Ringkasan piutang pelanggan toko Anda.
        </p>
    </div>


    {{-- RINGKASAN --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

        {{-- TOTAL PIUTANG --}}
        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-4 shadow-xl">

            <p class="text-xs text-gray-500">
                Total Piutang
            </p>

            <p class="text-lg font-bold text-amber-400 mt-1">
                Rp {{ number_format($totalPiutang, 0, ',', '.') }}
            </p>

            <p class="text-[11px] text-gray-600 mt-1">
                Sisa yang belum dibayar
            </p>

        </div>


        {{-- TOTAL TERBAYAR --}}
        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-4 shadow-xl">

            <p class="text-xs text-gray-500">
                Total Terbayar
            </p>

            <p class="text-lg font-bold text-emerald-400 mt-1">
                Rp {{ number_format($totalTerbayar, 0, ',', '.') }}
            </p>

            <p class="text-[11px] text-gray-600 mt-1">
                Total pembayaran pelanggan
            </p>

        </div>


        {{-- PELANGGAN --}}
        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-4 shadow-xl">

            <p class="text-xs text-gray-500">
                Pelanggan Berutang
            </p>

            <p class="text-lg font-bold text-white mt-1">
                {{ $jumlahPelanggan }}
            </p>

            <p class="text-[11px] text-gray-600 mt-1">
                Pelanggan dengan sisa piutang
            </p>

        </div>

    </div>


    {{-- DAFTAR PELANGGAN --}}
    <div class="bg-gray-800/80 border border-white/10
                rounded-2xl shadow-xl overflow-hidden">

        <div class="px-4 py-3 border-b border-white/10">

            <h2 class="text-sm font-semibold text-white">
                Daftar Piutang Pelanggan
            </h2>

            <p class="text-[11px] text-gray-500 mt-1">
                Rincian utang dan pembayaran pelanggan.
            </p>

        </div>


        <div class="divide-y divide-white/5">

            @forelse($customers as $customer)

                @php

                    $totalUtang = $customer->debts->sum('amount');

                    $totalBayar = $customer->debts->sum('paid_amount');

                    $sisaUtang = $customer->debts->sum(
                        function ($debt) {
                            return max(
                                0,
                                (float) $debt->amount -
                                (float) $debt->paid_amount
                            );
                        }
                    );

                @endphp


                {{-- HANYA TAMPILKAN YANG MASIH BERUTANG --}}
                @if($sisaUtang > 0)

                    <a
                        href="{{ route('pelanggan.show', $customer->id) }}"
                        class="block px-4 py-4
                               hover:bg-white/[0.03]
                               transition"
                    >

                        <div class="flex items-start justify-between gap-3">

                            {{-- INFO PELANGGAN --}}
                            <div class="min-w-0">

                                <div class="flex items-center gap-2">

                                    <div class="w-9 h-9
                                                rounded-xl
                                                bg-emerald-500/10
                                                border border-emerald-500/20
                                                flex items-center justify-center
                                                text-sm">
                                        👤
                                    </div>

                                    <div class="min-w-0">

                                        <p class="text-sm font-semibold text-white truncate">
                                            {{ $customer->name }}
                                        </p>

                                        @if($customer->phone)
                                            <p class="text-[11px] text-gray-500">
                                                📱 {{ $customer->phone }}
                                            </p>
                                        @endif

                                    </div>

                                </div>

                            </div>


                            {{-- SISA --}}
                            <div class="text-right shrink-0">

                                <p class="text-sm font-bold text-amber-400">
                                    Rp {{ number_format($sisaUtang, 0, ',', '.') }}
                                </p>

                                <p class="text-[10px] text-gray-500 mt-1">
                                    Sisa piutang
                                </p>

                            </div>

                        </div>


                        {{-- DETAIL --}}
                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1
                                    text-[11px] text-gray-500">

                            <span>
                                Total utang:
                                <span class="text-gray-300">
                                    Rp {{ number_format($totalUtang, 0, ',', '.') }}
                                </span>
                            </span>

                            <span>
                                Terbayar:
                                <span class="text-emerald-400">
                                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                </span>
                            </span>

                        </div>

                    </a>

                @endif

            @empty

                <div class="px-4 py-10 text-center">

                    <div class="text-3xl mb-3">
                        📭
                    </div>

                    <p class="text-sm text-gray-400">
                        Belum ada data pelanggan.
                    </p>

                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection