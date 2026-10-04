@extends('layouts.app')

@section('title', 'Pembelian')
@section('header', '🛒')

@section('content')

<div class="space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center justify-between gap-3">

        <div>
            <h1 class="text-lg font-semibold text-white">
                Pembelian
            </h1>

            <p class="text-xs text-gray-500 mt-1">
                Kelola pembelian dan stok masuk dari supplier.
            </p>
        </div>

        <a
            href="{{ route('purchase.create') }}"
            class="inline-flex items-center gap-2
                   bg-emerald-500
                   hover:bg-emerald-400
                   text-gray-950
                   px-3 py-2
                   rounded-xl
                   text-xs
                   font-semibold
                   transition
                   whitespace-nowrap"
        >
            <span>＋</span>
            <span>Tambah Pembelian</span>
        </a>

    </div>

    {{-- LIST PEMBELIAN --}}
    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               overflow-hidden"
    >

        <div class="px-4 py-3 border-b border-white/10">

            <p class="text-xs text-gray-400">
                {{ $purchases->total() }} pembelian tercatat
            </p>

        </div>

        @forelse($purchases as $purchase)

    @php
        $purchasedQty = $purchase->items->sum('quantity');

        $returnedQty = $purchase->items->sum(function ($item) {
            return $item->returnItems->sum('quantity');
        });

        $canReturn = $returnedQty < $purchasedQty;
    @endphp

    <div
        class="px-4 py-4
               border-b border-white/5
               last:border-b-0
               hover:bg-white/[0.03]
               transition"
    >

        <div class="flex items-center justify-between gap-3">

            {{-- INFORMASI PEMBELIAN --}}
            <a
                href="{{ route('purchase.show', $purchase->id) }}"
                class="min-w-0 flex-1"
            >

                <h3 class="text-sm font-semibold text-white truncate">
                    {{ $purchase->supplier->name ?? 'Supplier tidak ditemukan' }}
                </h3>

                <p class="text-xs text-gray-500 mt-1">
                    {{ $purchase->invoice_number ?: 'Tanpa nomor invoice' }}
                </p>

                <p class="text-xs text-gray-500 mt-1">
                    📅 {{ $purchase->purchase_date->format('d/m/Y') }}
                </p>

            </a>


            {{-- TOTAL + ACTION --}}
            <div class="text-right shrink-0">

                <p class="text-sm font-semibold text-emerald-400">
                    Rp {{ number_format($purchase->total, 0, ',', '.') }}
                </p>

                <p class="text-[11px] text-gray-500 mt-1">
                    {{ $purchasedQty }} pcs
                    <span class="text-gray-700">•</span>
                    {{ $purchase->items->count() }} jenis
                </p>


                {{-- ACTION --}}
                <div class="flex items-center justify-end gap-2 mt-2">

                    {{-- DETAIL --}}
                    <a
                        href="{{ route('purchase.show', $purchase->id) }}"
                        class="inline-flex items-center
                               px-2.5 py-1.5
                               rounded-lg
                               text-[10px]
                               font-semibold
                               text-gray-300
                               bg-white/5
                               hover:bg-white/10
                               transition"
                    >
                        Detail
                    </a>


                    {{-- RETUR --}}
                    @if($canReturn)

                        <a
                            href="{{ route('purchase.return.create', $purchase->id) }}"
                            class="inline-flex items-center
                                   gap-1
                                   px-2.5 py-1.5
                                   rounded-lg
                                   text-[10px]
                                   font-semibold
                                   text-amber-300
                                   bg-amber-500/10
                                   border border-amber-500/20
                                   hover:bg-amber-500/20
                                   transition"
                        >
                            ↩️ Retur
                        </a>

                    @endif

                </div>

            </div>

        </div>

    </div>

@empty

            <div class="px-4 py-12 text-center">

                <div class="text-3xl mb-3">
                    🛒
                </div>

                <h3 class="text-sm font-semibold text-white">
                    Belum ada pembelian
                </h3>

                <p class="text-xs text-gray-500 mt-2">
                    Pembelian dari supplier akan muncul di sini.
                </p>

            </div>

        @endforelse

    </div>

    {{-- PAGINATION --}}
    @if($purchases->hasPages())
        <div>
            {{ $purchases->links() }}
        </div>
    @endif

</div>

@endsection