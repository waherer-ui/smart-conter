@extends('layouts.app')

@section('title', 'Retur Penjualan')

@section('content')

<div class="max-w-3xl mx-auto space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center justify-between gap-3">

        <div>
            <h1 class="text-lg font-semibold text-white">
                Retur Penjualan
            </h1>

            <p class="text-xs text-gray-500 mt-1">
                Pilih barang yang dikembalikan oleh pelanggan.
            </p>
        </div>

        <a
            href="{{ route('transaksi.show', $transaction->id) }}"
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
                    Pelanggan
                </div>

                <div class="text-sm text-white mt-1">
                    {{ $transaction->customer->name ?? 'Umum' }}
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

        </div>

    </div>


    {{-- FORM RETUR --}}
    <form
        action="{{ route('transaction.return.store', $transaction->id) }}"
        method="POST"
        class="space-y-4"
    >

        @csrf


        {{-- BARANG --}}
        <div class="bg-gray-800/80 border border-white/10 rounded-2xl shadow-xl overflow-hidden">

            <div class="p-5 border-b border-white/10">

                <h2 class="text-sm font-semibold text-white">
                    Barang Penjualan
                </h2>

                <p class="text-[11px] text-gray-500 mt-1">
                    Masukkan jumlah barang yang ingin dikembalikan.
                </p>

            </div>


            <div class="divide-y divide-white/5">

                @foreach($transaction->items as $item)

                    @php
                        $soldQuantity = (int) $item->quantity;

                        $returnedQuantity = (int)
                            $item->returnItems->sum('quantity');

                        $remainingQuantity = max(
                            0,
                            $soldQuantity - $returnedQuantity
                        );
                    @endphp


                    <div class="p-5">

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

                            <div>

                                <div class="text-sm font-medium text-white">
                                    {{ $item->product_name }}
                                </div>

                                <div class="text-[10px] text-gray-500 mt-1">
                                    SKU: {{ $item->sku ?? '-' }}
                                </div>

                                <div class="text-[10px] text-gray-400 mt-2">
                                    Harga jual:
                                    <span class="text-gray-300">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </span>
                                </div>

                            </div>


                            <div class="text-left sm:text-right">

                                <div class="text-[10px] text-gray-500">
                                    Terjual
                                </div>

                                <div class="text-sm text-white font-semibold">
                                    {{ $soldQuantity }}
                                </div>

                            </div>

                        </div>


                        @if($remainingQuantity > 0)

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">

                                <div>

                                    <label class="block text-[10px] text-gray-400 mb-1">
                                        Jumlah Retur
                                    </label>

                                    <input
                                        type="number"
                                        name="items[{{ $item->id }}][quantity]"
                                        value="0"
                                        min="0"
                                        max="{{ $remainingQuantity }}"
                                        class="w-full bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-xs text-white outline-none focus:ring-2 focus:ring-amber-500"
                                    >

                                </div>


                                <div>

                                    <div class="text-[10px] text-gray-500">
                                        Sudah diretur
                                    </div>

                                    <div class="text-xs text-amber-400 mt-1">
                                        {{ $returnedQuantity }}
                                    </div>

                                </div>


                                <div>

                                    <div class="text-[10px] text-gray-500">
                                        Maksimal retur
                                    </div>

                                    <div class="text-xs text-emerald-400 mt-1">
                                        {{ $remainingQuantity }}
                                    </div>

                                </div>

                            </div>

                        @else

                            <div class="mt-4 bg-gray-900/70 border border-white/5 rounded-xl px-3 py-2">

                                <div class="text-[10px] text-gray-500">
                                    Status
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    Barang sudah diretur seluruhnya.
                                </div>

                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

        </div>


        {{-- ALASAN & CATATAN --}}
        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

            <div>

                <label
                    for="reason"
                    class="block text-xs text-gray-400 mb-1"
                >
                    Alasan Retur
                </label>

                <input
                    type="text"
                    id="reason"
                    name="reason"
                    value="{{ old('reason') }}"
                    maxlength="255"
                    placeholder="Contoh: Barang rusak"
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-xs text-white outline-none focus:ring-2 focus:ring-amber-500"
                >

            </div>


            <div class="mt-4">

                <label
                    for="notes"
                    class="block text-xs text-gray-400 mb-1"
                >
                    Catatan
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="3"
                    placeholder="Catatan tambahan..."
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-xs text-white outline-none focus:ring-2 focus:ring-amber-500"
                >{{ old('notes') }}</textarea>

            </div>

        </div>


        {{-- ERROR --}}
        @if($errors->any())

            <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4">

                <div class="text-xs font-semibold text-red-400">
                    Terjadi kesalahan:
                </div>

                <ul class="mt-2 space-y-1">

                    @foreach($errors->all() as $error)

                        <li class="text-[11px] text-red-300">
                            • {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- TOMBOL --}}
        <div class="flex flex-col sm:flex-row gap-2">

            <a
                href="{{ route('transaksi.show', $transaction->id) }}"
                class="flex-1 text-center bg-gray-700 hover:bg-gray-600 text-white px-4 py-3 rounded-xl text-xs font-semibold transition"
            >
                Batal
            </a>


            @if ($transaction->items->contains(function ($item) {
                $returnedQuantity = $item->returnItems->sum('quantity');
                $remainingQuantity = $item->quantity - $returnedQuantity;
            
                return $remainingQuantity > 0;
            }))
                <button
                    type="submit"
                    class="flex-1 bg-amber-600 hover:bg-amber-500 text-white px-4 py-3 rounded-xl text-xs font-semibold transition"
                >
                    ↩️ Proses Retur
                </button>
            @endif

        </div>

    </form>

</div>

@endsection
