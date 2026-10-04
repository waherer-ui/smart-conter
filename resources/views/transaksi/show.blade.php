@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('header', '🧾')

@section('content')

<div class="max-w-4xl mx-auto space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center justify-between gap-3">

        <div>
            <h1 class="text-lg font-semibold text-white">
                Detail Transaksi
            </h1>

            <p class="text-xs text-gray-500 mt-1">
                Informasi lengkap transaksi penjualan.
            </p>
        </div>

        <a
            href="{{ route('riwayat') }}"
            class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl text-xs font-semibold transition"
        >
            ← Kembali
        </a>

    </div>


    {{-- INFORMASI TRANSAKSI --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div>
                <div class="text-[10px] text-gray-500 uppercase">
                    Invoice
                </div>

                <div class="text-sm text-indigo-400 font-mono mt-1">
                    {{ $transaction->invoice_number }}
                </div>
            </div>


            <div>
                <div class="text-[10px] text-gray-500 uppercase">
                    Tanggal
                </div>

                <div class="text-sm text-white mt-1">
                    {{ $transaction->created_at->format('d/m/Y H:i:s') }}
                </div>
            </div>


            <div>
                <div class="text-[10px] text-gray-500 uppercase">
                    Kasir
                </div>

                <div class="text-sm text-white mt-1">
                    {{ $transaction->user->name ?? '-' }}
                </div>
            </div>


            <div>
                <div class="text-[10px] text-gray-500 uppercase">
                    Pelanggan
                </div>

                <div class="text-sm text-white mt-1">
                    {{ $transaction->customer->name ?? 'Umum' }}
                </div>
            </div>


            <div>
                <div class="text-[10px] text-gray-500 uppercase">
                    Pembayaran
                </div>

                <div class="text-sm text-gray-300 mt-1">
                    {{ $transaction->payment_method }}
                </div>
            </div>

        </div>

    </div>


    {{-- BARANG --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl shadow-xl overflow-hidden">

        <div class="p-5 border-b border-white/10">

            <h2 class="text-sm font-semibold text-white">
                Barang
            </h2>

            <p class="text-[11px] text-gray-500 mt-1">
                Detail barang yang terjual pada transaksi ini.
            </p>

        </div>


        <div class="divide-y divide-white/5">

            @foreach($transaction->items as $item)

                @php
                    $soldQuantity = (int) $item->quantity;

                    $returnedQuantity = (int)
                        $item->returnItems->sum('quantity');

                    $remainingQuantity =
                        max(
                            0,
                            $soldQuantity - $returnedQuantity
                        );
                @endphp

                <div class="p-5">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                        <div>

                            <div class="text-sm font-medium text-white">
                                {{ $item->product_name }}
                            </div>

                            <div class="text-[10px] text-gray-500 mt-1">
                                SKU: {{ $item->sku ?? '-' }}
                            </div>

                        </div>


                        <div class="text-left sm:text-right">

                            <div class="text-sm text-emerald-400 font-semibold">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>

                            <div class="text-[10px] text-gray-500 mt-1">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                                × {{ $soldQuantity }}
                            </div>

                        </div>

                    </div>


                    {{-- STATUS RETUR --}}
                    <div class="mt-3 flex flex-wrap gap-2">

                        <span class="bg-gray-700/70 border border-white/5 text-gray-300 px-2 py-1 rounded-lg text-[10px]">
                            Terjual: {{ $soldQuantity }}
                        </span>

                        @if($returnedQuantity > 0)

                            <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 px-2 py-1 rounded-lg text-[10px]">
                                Sudah retur: {{ $returnedQuantity }}
                            </span>

                        @endif


                        @if($remainingQuantity > 0)

                            <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-2 py-1 rounded-lg text-[10px]">
                                Bisa retur: {{ $remainingQuantity }}
                            </span>

                        @else

                            <span class="bg-gray-700 border border-white/5 text-gray-500 px-2 py-1 rounded-lg text-[10px]">
                                Sudah diretur seluruhnya
                            </span>

                        @endif

                    </div>

                </div>

            @endforeach

        </div>

    </div>


    {{-- RINGKASAN --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

        <div class="space-y-2">

            <div class="flex justify-between text-xs">

                <span class="text-gray-400">
                    Subtotal
                </span>

                <span class="text-gray-300">
                    Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}
                </span>

            </div>


            <div class="flex justify-between text-xs">

                <span class="text-gray-400">
                    Diskon
                </span>

                <span class="text-gray-300">
                    Rp {{ number_format($transaction->discount, 0, ',', '.') }}
                </span>

            </div>


            <div class="flex justify-between pt-2 border-t border-white/10">

                <span class="text-sm font-bold text-white">
                    TOTAL
                </span>

                <span class="text-sm font-bold text-emerald-400">
                    Rp {{ number_format($transaction->total, 0, ',', '.') }}
                </span>

            </div>

        </div>

    </div>


    {{-- AKSI --}}
    <div class="flex flex-col sm:flex-row gap-2">

        <a
            href="{{ route('riwayat') }}"
            class="flex-1 text-center bg-gray-700 hover:bg-gray-600 text-white px-4 py-3 rounded-xl text-xs font-semibold transition"
        >
            ← Kembali
        </a>


        <a
            href="{{ route('transaksi.struk.pdf', $transaction->id) }}"
            target="_blank"
            class="flex-1 text-center bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-3 rounded-xl text-xs font-semibold transition"
        >
            🧾 Cetak Struk
        </a>


        @if($transaction->items->contains(function ($item) {
            return $item->returnItems->sum('quantity') < $item->quantity;
        }))

            <a
                href="{{ route('transaction.return.create', $transaction->id) }}"
                class="flex-1 text-center bg-amber-600 hover:bg-amber-500 text-white px-4 py-3 rounded-xl text-xs font-semibold transition"
            >
                ↩️ Retur
            </a>

        @endif

    </div>

</div>

@endsection
