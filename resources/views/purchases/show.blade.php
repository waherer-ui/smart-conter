@extends('layouts.app')

@section('title', 'Detail Pembelian')
@section('header', '🛒')

@section('content')

<div class="max-w-3xl mx-auto space-y-4">

   {{-- HEADER --}}
<div class="flex items-center justify-between gap-3">

    <div>
        <h1 class="text-lg font-semibold text-white">
            Detail Pembelian
        </h1>

        <p class="text-xs text-gray-500 mt-1">
            Informasi lengkap pembelian dan stok masuk.
        </p>
    </div>

    <div class="flex items-center gap-2">


        {{-- KEMBALI --}}
        <a
            href="{{ route('purchase.index') }}"
            class="px-3 py-2
                   rounded-xl
                   text-xs
                   font-semibold
                   text-gray-400
                   hover:text-white
                   hover:bg-white/5
                   transition
                   whitespace-nowrap"
        >
            ← Kembali
        </a>

    </div>

</div>

    {{-- INFORMASI PEMBELIAN --}}
    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               p-5
               space-y-4"
    >

        <div class="flex items-start justify-between gap-4">

            <div>
                <p class="text-[11px] text-gray-500">
                    Supplier
                </p>

                <p class="text-sm font-semibold text-white mt-1">
                    {{ $purchase->supplier->name ?? 'Supplier tidak ditemukan' }}
                </p>
            </div>

            <div class="text-right">
                <p class="text-[11px] text-gray-500">
                    Invoice
                </p>

                <p class="text-sm font-semibold text-emerald-400 mt-1">
                    {{ $purchase->invoice_number ?: 'Tanpa nomor invoice' }}
                </p>
            </div>

        </div>

        <div class="border-t border-white/5 pt-4">

            <div class="grid grid-cols-2 gap-4">

                <div>
                    <p class="text-[11px] text-gray-500">
                        Tanggal Pembelian
                    </p>

                    <p class="text-xs text-gray-300 mt-1">
                        📅 {{ $purchase->purchase_date->format('d/m/Y') }}
                    </p>
                </div>

                <div>
                    <p class="text-[11px] text-gray-500">
                        Dibuat Oleh
                    </p>

                    <p class="text-xs text-gray-300 mt-1">
                        {{ $purchase->user->name ?? $purchase->user->username ?? '-' }}
                    </p>
                </div>

            </div>

        </div>

    </div>

    {{-- PRODUK --}}
    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               overflow-hidden"
    >

        <div class="px-5 py-4 border-b border-white/10">

            <h2 class="text-sm font-semibold text-white">
                Barang Masuk
            </h2>

        </div>

        @foreach($purchase->items as $item)

            <div class="px-5 py-4 border-b border-white/5 last:border-b-0">

                <div class="flex items-start justify-between gap-4">

                    <div class="min-w-0">

                        <p class="text-sm font-semibold text-white">
                            {{ $item->product_name }}
                        </p>

                        <p class="text-[11px] text-gray-500 mt-1">
                            SKU: {{ $item->sku ?: '-' }}
                        </p>

                    </div>

                    <div class="text-right shrink-0">

                        <p class="text-sm font-semibold text-emerald-400">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </p>

                        <p class="text-[11px] text-gray-500 mt-1">
                            {{ number_format($item->quantity, 0, ',', '.') }} pcs
                            ×
                            Rp {{ number_format($item->capital_price, 0, ',', '.') }}
                        </p>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

    {{-- TOTAL --}}
    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               p-5"
    >

        <div class="flex items-center justify-between">

            <span class="text-sm text-gray-400">
                Total Pembelian
            </span>

            <span class="text-lg font-bold text-emerald-400">
                Rp {{ number_format($purchase->total, 0, ',', '.') }}
            </span>

        </div>

    </div>

    {{-- CATATAN --}}
    @if($purchase->notes)

        <div
            class="bg-gray-800/80
                   border border-white/10
                   rounded-2xl
                   shadow-xl
                   p-5"
        >

            <p class="text-xs font-semibold text-gray-400">
                Catatan
            </p>

            <p class="text-sm text-gray-300 mt-2 whitespace-pre-line">
                {{ $purchase->notes }}
            </p>

        </div>

    @endif

</div>

@endsection