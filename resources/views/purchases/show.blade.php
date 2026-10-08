@extends('layouts.app')

@section('title', 'Detail Pembelian')

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

      <a
    href="{{ route('purchase.return.create', $purchase->id) }}"
    class="px-3 py-2
           rounded-xl
           text-xs
           font-semibold
           text-amber-400
           bg-amber-400/10
           hover:bg-amber-400/20
           transition
           whitespace-nowrap"
>
    ↩️ Retur Supplier
</a>

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

              @php
                  $returnedQty = $item->returnItems->sum('quantity');
                  $netQty = max(
                      0,
                      $item->quantity - $returnedQty
                  );
              @endphp

              @if($returnedQty > 0)

                  <div class="mt-2 space-y-1">

                      <p class="text-[10px] text-amber-400">
                          Diretur:
                          {{ number_format($returnedQty, 0, ',', '.') }} pcs
                      </p>

                      <p class="text-[10px] text-gray-400">
                          Net diterima:
                          <span class="text-gray-300 font-semibold">
                              {{ number_format($netQty, 0, ',', '.') }} pcs
                          </span>
                      </p>

                  </div>

              @endif

          </div>

                </div>

            </div>

        @endforeach

    </div>

{{-- TOTAL PEMBELIAN --}}
@php
    $totalReturn = $purchase->returns->sum('total');
    $netPurchase = max(
        0,
        (float) $purchase->total - (float) $totalReturn
    );
@endphp

<div
    class="bg-gray-800/80
           border border-white/10
           rounded-2xl
           shadow-xl
           p-5
           space-y-3"
>

    {{-- TOTAL PEMBELIAN --}}
    <div class="flex items-center justify-between">

        <span class="text-sm text-gray-400">
            Total Pembelian
        </span>

        <span class="text-lg font-bold text-emerald-400">
            Rp {{ number_format($purchase->total, 0, ',', '.') }}
        </span>

    </div>

    {{-- TOTAL RETUR --}}
    @if($totalReturn > 0)

        <div class="flex items-center justify-between">

            <span class="text-sm text-gray-400">
                Total Retur
            </span>

            <span class="text-sm font-semibold text-amber-400">
                Rp {{ number_format($totalReturn, 0, ',', '.') }}
            </span>

        </div>

        {{-- NILAI BERSIH --}}
        <div class="border-t border-white/5 pt-3">

            <div class="flex items-center justify-between">

                <span class="text-sm font-semibold text-gray-300">
                    Nilai Bersih
                </span>

                <span class="text-lg font-bold text-white">
                    Rp {{ number_format($netPurchase, 0, ',', '.') }}
                </span>

            </div>

        </div>

    @endif

</div>

    {{-- RIWAYAT RETUR SUPPLIER --}}
@if($purchase->returns->count() > 0)

    <div
        class="bg-gray-800/80
               border border-amber-400/10
               rounded-2xl
               shadow-xl
               overflow-hidden"
    >

        <div class="px-5 py-4 border-b border-white/10">

            <div class="flex items-center justify-between gap-3">

                <div>
                    <h2 class="text-sm font-semibold text-white">
                        Riwayat Retur Supplier
                    </h2>

                    <p class="text-[11px] text-gray-500 mt-1">
                        {{ $purchase->returns->count() }} retur telah dibuat.
                    </p>
                </div>

                <span class="text-xs font-semibold text-amber-400">
                    ↩️ Retur
                </span>

            </div>

        </div>

        @foreach($purchase->returns->sortByDesc('created_at') as $return)

            <div class="px-5 py-4 border-b border-white/5 last:border-b-0">

                <div class="flex items-start justify-between gap-4">

                    <div class="min-w-0">

                        <p class="text-sm font-semibold text-white">
                            {{ $return->return_number }}
                        </p>

                        <p class="text-[11px] text-gray-500 mt-1">
                            📅 {{ $return->return_date->format('d/m/Y') }}
                        </p>

                        @if($return->reason)
                            <p class="text-[11px] text-gray-400 mt-2">
                                Alasan:
                                <span class="text-gray-300">
                                    {{ $return->reason }}
                                </span>
                            </p>
                        @endif

                        <p class="text-[11px] text-gray-500 mt-2">
                            Oleh:
                            <span class="text-gray-400">
                                {{ $return->user->name ?? $return->user->username ?? '-' }}
                            </span>
                        </p>

                    </div>

                    <div class="text-right shrink-0">

                        <p class="text-sm font-semibold text-amber-400">
                            Rp {{ number_format($return->total, 0, ',', '.') }}
                        </p>

                    </div>

                </div>

                {{-- ITEM RETUR --}}
                <div class="mt-4 space-y-2">

                    @foreach($return->items as $returnItem)

                        <div
                            class="flex items-center justify-between gap-3
                                   bg-black/10
                                   rounded-xl
                                   px-3
                                   py-2"
                        >

                            <div class="min-w-0">

                                <p class="text-xs text-gray-300 truncate">
                                    {{ $returnItem->product_name }}
                                </p>

                                <p class="text-[10px] text-gray-500 mt-1">
                                    SKU: {{ $returnItem->sku ?: '-' }}
                                </p>

                            </div>

                            <div class="text-right shrink-0">

                                <p class="text-xs font-semibold text-gray-300">
                                    {{ number_format($returnItem->quantity, 0, ',', '.') }} pcs
                                </p>

                                <p class="text-[10px] text-gray-500">
                                    Rp {{ number_format($returnItem->subtotal, 0, ',', '.') }}
                                </p>

                            </div>

                        </div>

                    @endforeach

                </div>

                @if($return->notes)

                    <div class="mt-3 pt-3 border-t border-white/5">

                        <p class="text-[10px] text-gray-500">
                            Catatan
                        </p>

                        <p class="text-xs text-gray-400 mt-1 whitespace-pre-line">
                            {{ $return->notes }}
                        </p>

                    </div>

                @endif

            </div>

        @endforeach

    </div>

@endif

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