@extends('layouts.app')

@section('title', 'Detail Transfer')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('transfer.index') }}"
                   class="text-gray-400 hover:text-white text-sm">
                    ← Kembali
                </a>
            </div>

            <h1 class="text-2xl font-bold text-white">
                Detail Transfer
            </h1>

            <p class="text-sm text-gray-400 mt-1">
                #TRF-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}
            </p>
        </div>

        {{-- Status --}}
        @if($transfer->status === 'pending')
            <span class="inline-flex items-center w-fit
                         px-3 py-1.5 rounded-full
                         bg-yellow-500/10
                         border border-yellow-500/20
                         text-yellow-300 text-sm font-semibold">
                Menunggu Penerimaan
            </span>
        @elseif($transfer->status === 'completed')
            <span class="inline-flex items-center w-fit
                         px-3 py-1.5 rounded-full
                         bg-emerald-500/10
                         border border-emerald-500/20
                         text-emerald-300 text-sm font-semibold">
                Selesai
            </span>
        @elseif($transfer->status === 'cancelled')
            <span class="inline-flex items-center w-fit
                         px-3 py-1.5 rounded-full
                         bg-red-500/10
                         border border-red-500/20
                         text-red-300 text-sm font-semibold">
                Dibatalkan
            </span>
        @endif
    </div>


    {{-- Informasi Transfer --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-5 mb-5">

        <h2 class="text-sm font-semibold text-gray-300 mb-4">
            Informasi Transfer
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <p class="text-xs text-gray-500 mb-1">
                    Toko Asal
                </p>

                <p class="text-white font-semibold">
                    {{ $transfer->sourceStore->name ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-500 mb-1">
                    Toko Tujuan
                </p>

                <p class="text-emerald-400 font-semibold">
                    {{ $transfer->destinationStore->name ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-500 mb-1">
                    Tanggal Transfer
                </p>

                <p class="text-white">
                    {{ optional($transfer->transfer_date)->format('d M Y') }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-500 mb-1">
                    Dibuat Oleh
                </p>

                <p class="text-white">
                    {{ $transfer->user->name ?? '-' }}
                </p>
            </div>

        </div>

        @if($transfer->notes)
            <div class="mt-5 pt-5 border-t border-gray-700">
                <p class="text-xs text-gray-500 mb-1">
                    Catatan
                </p>

                <p class="text-sm text-gray-300 whitespace-pre-line">
                    {{ $transfer->notes }}
                </p>
            </div>
        @endif

    </div>


    {{-- Daftar Produk --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-700">
            <h2 class="text-sm font-semibold text-gray-300">
                Produk yang Ditransfer
            </h2>
        </div>

        <div class="divide-y divide-gray-700">

            @forelse($transfer->items as $item)

                <div class="p-4 sm:p-5">

                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">
                            <p class="text-white font-semibold truncate">
                                {{ $item->product_name }}
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                SKU: {{ $item->sku }}
                            </p>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <p class="text-lg font-bold text-emerald-400">
                                {{ number_format($item->quantity) }}
                            </p>

                            <p class="text-xs text-gray-500">
                                pcs
                            </p>
                        </div>

                    </div>

                </div>

            @empty

                <div class="p-8 text-center text-gray-400 text-sm">
                    Tidak ada produk dalam transfer.
                </div>

            @endforelse

        </div>


        {{-- Total --}}
        @if($transfer->items->count())
            <div class="px-5 py-4 border-t border-gray-700
                        flex items-center justify-between">
                <span class="text-sm text-gray-400">
                    Total Qty
                </span>

                <span class="text-white font-bold">
                    {{ number_format($transfer->items->sum('quantity')) }} pcs
                </span>
            </div>
        @endif

    </div>


{{-- Aksi --}}
@if($transfer->status === 'pending')

    @if((int) session('active_store_id') === (int) $transfer->destination_store_id)

        <div class="mt-5 rounded-2xl
                    border border-emerald-500/20
                    bg-emerald-500/5
                    p-5">

            <div class="flex gap-3">

                <div class="text-xl">
                    📦
                </div>

                <div class="flex-1">

                    <h3 class="text-sm font-semibold text-emerald-300">
                        Barang Siap Diterima
                    </h3>

                    <p class="text-xs text-gray-400 mt-1">
                        Pastikan barang sudah diterima secara fisik
                        sebelum mengonfirmasi penerimaan.
                    </p>

                    <form
                        action="{{ route('transfer.receive', $transfer->id) }}"
                        method="POST"
                        class="mt-4"
                        onsubmit="return confirm('Apakah Anda yakin ingin menerima transfer ini? Stok toko tujuan akan bertambah.');"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-500
                                   active:bg-emerald-700
                                   text-white font-semibold
                                   py-3 px-4 rounded-xl
                                   transition"
                        >
                            📦 Terima Barang
                        </button>

                    </form>

                </div>

            </div>

        </div>

    @else

        <div class="mt-5 rounded-2xl
                    border border-yellow-500/20
                    bg-yellow-500/5
                    p-5">

            <div class="flex gap-3">

                <div class="text-xl">
                    📦
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-yellow-300">
                        Transfer Menunggu Penerimaan
                    </h3>

                    <p class="text-xs text-gray-400 mt-1">
                        Stok toko tujuan belum bertambah.
                        Barang akan masuk ke stok setelah
                        transfer diterima oleh toko tujuan.
                    </p>
                </div>

            </div>

        </div>

    @endif

@elseif($transfer->status === 'completed')

    <div class="mt-5 rounded-2xl
                border border-emerald-500/20
                bg-emerald-500/5
                p-5">

        <div class="flex gap-3">

            <div class="text-xl">
                ✅
            </div>

            <div>
                <h3 class="text-sm font-semibold text-emerald-300">
                    Transfer Telah Diterima
                </h3>

                <p class="text-xs text-gray-400 mt-1">
                    Barang telah diterima dan stok toko tujuan
                    sudah bertambah.
                </p>
            </div>

        </div>

    </div>

@endif

</div>
@endsection