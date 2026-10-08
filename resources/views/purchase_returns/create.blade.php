@extends('layouts.app')

@section('title', 'Retur Supplier')
@section('content')

<div class="max-w-3xl mx-auto space-y-4">{{-- HEADER --}}
<div class="flex items-center justify-between gap-3">

    <div>
        <h1 class="text-lg font-semibold text-white">
            Retur Supplier
        </h1>

        <p class="text-xs text-gray-500 mt-1">
            Pilih barang yang akan dikembalikan kepada supplier.
        </p>
    </div>

    <div class="flex items-center gap-2">

        <a
            href="{{ route('purchase.show', $purchase->id) }}"
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
                    Total Pembelian
                </p>

                <p class="text-xs font-semibold text-emerald-400 mt-1">
                    Rp {{ number_format($purchase->total, 0, ',', '.') }}
                </p>

            </div>

        </div>

    </div>

</div>


{{-- FORM RETUR --}}
<form
    method="POST"
    action="{{ route('purchase.return.store', $purchase->id) }}"
    class="space-y-4"
>

    @csrf


    {{-- BARANG PEMBELIAN --}}
    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               overflow-hidden"
    >

        <div class="px-5 py-4 border-b border-white/10">

            <h2 class="text-sm font-semibold text-white">
                Barang Pembelian
            </h2>

            <p class="text-[11px] text-gray-500 mt-1">
                Masukkan jumlah barang yang ingin dikembalikan.
            </p>

        </div>


        @foreach($purchase->items as $item)

            @php
                $maxReturn = min(
                    $item->returnable_quantity,
                    $item->current_stock
                );
            @endphp


            <div
                class="px-5 py-4
                       border-b border-white/5
                       last:border-b-0"
            >

                {{-- DATA ITEM --}}
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
                            Rp {{ number_format($item->capital_price, 0, ',', '.') }}
                        </p>

                        <p class="text-[11px] text-gray-500 mt-1">
                            / pcs
                        </p>

                    </div>

                </div>


                {{-- INFORMASI JUMLAH --}}
                <div class="grid grid-cols-3 gap-3 mt-4">

                    <div
                        class="bg-gray-900/50
                               border border-white/5
                               rounded-xl
                               p-3"
                    >

                        <p class="text-[10px] text-gray-500">
                            Dibeli
                        </p>

                        <p class="text-sm font-semibold text-gray-300 mt-1">
                            {{ number_format($item->quantity, 0, ',', '.') }}
                        </p>

                    </div>


                    <div
                        class="bg-gray-900/50
                               border border-white/5
                               rounded-xl
                               p-3"
                    >

                        <p class="text-[10px] text-gray-500">
                            Sudah Retur
                        </p>

                        <p class="text-sm font-semibold text-yellow-400 mt-1">
                            {{ number_format($item->already_returned, 0, ',', '.') }}
                        </p>

                    </div>


                    <div
                        class="bg-gray-900/50
                               border border-white/5
                               rounded-xl
                               p-3"
                    >

                        <p class="text-[10px] text-gray-500">
                            Stok Saat Ini
                        </p>

                        <p class="text-sm font-semibold text-blue-400 mt-1">
                            {{ number_format($item->current_stock, 0, ',', '.') }}
                        </p>

                    </div>

                </div>


                {{-- INPUT RETUR --}}
                <div class="mt-4">

                    <label
                        for="quantity_{{ $item->id }}"
                        class="block text-xs font-semibold text-gray-400 mb-2"
                    >
                        Jumlah Retur
                    </label>


                    @if($maxReturn > 0)

                        <input
                            type="number"
                            id="quantity_{{ $item->id }}"
                            name="quantity[{{ $item->id }}]"
                            min="0"
                            max="{{ $maxReturn }}"
                            value="{{ old('quantity.' . $item->id, 0) }}"
                            inputmode="numeric"
                            class="w-full
                                   rounded-xl
                                   bg-gray-900
                                   border border-white/10
                                   px-4 py-3
                                   text-sm
                                   text-white
                                   outline-none
                                   focus:border-emerald-500
                                   focus:ring-1
                                   focus:ring-emerald-500"
                        >

                        <p class="text-[10px] text-gray-500 mt-2">
                            Maksimal dapat diretur:
                            <span class="text-emerald-400 font-semibold">
                                {{ number_format($maxReturn, 0, ',', '.') }} pcs
                            </span>
                        </p>

                    @else

                        <div
                            class="rounded-xl
                                   bg-gray-900/50
                                   border border-white/5
                                   px-4 py-3"
                        >

                            <p class="text-xs text-gray-500">
                                Barang ini tidak dapat diretur.
                            </p>


                            @if($item->returnable_quantity <= 0)

                                <p class="text-[10px] text-gray-600 mt-1">
                                    Seluruh jumlah pembelian sudah diretur.
                                </p>

                            @elseif($item->current_stock <= 0)

                                <p class="text-[10px] text-gray-600 mt-1">
                                    Stok saat ini tidak mencukupi.
                                </p>

                            @endif

                        </div>


                        <input
                            type="hidden"
                            name="quantity[{{ $item->id }}]"
                            value="0"
                        >

                    @endif

                </div>

            </div>

        @endforeach

    </div>


    {{-- ALASAN & CATATAN --}}
    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               p-5
               space-y-4"
    >

        <div>

            <label
                for="reason"
                class="block text-xs font-semibold text-gray-400 mb-2"
            >
                Alasan Retur
            </label>

            <select
                id="reason"
                name="reason"
                class="w-full
                       rounded-xl
                       bg-gray-900
                       border border-white/10
                       px-4 py-3
                       text-sm
                       text-white
                       outline-none
                       focus:border-emerald-500
                       focus:ring-1
                       focus:ring-emerald-500"
            >

                <option value="">
                    Pilih alasan
                </option>

                <option
                    value="Barang rusak"
                    @selected(old('reason') === 'Barang rusak')
                >
                    Barang rusak
                </option>

                <option
                    value="Barang cacat"
                    @selected(old('reason') === 'Barang cacat')
                >
                    Barang cacat
                </option>

                <option
                    value="Barang salah"
                    @selected(old('reason') === 'Barang salah')
                >
                    Barang salah
                </option>

                <option
                    value="Barang tidak sesuai"
                    @selected(old('reason') === 'Barang tidak sesuai')
                >
                    Barang tidak sesuai
                </option>

                <option
                    value="Kelebihan barang"
                    @selected(old('reason') === 'Kelebihan barang')
                >
                    Kelebihan barang
                </option>

                <option
                    value="Lainnya"
                    @selected(old('reason') === 'Lainnya')
                >
                    Lainnya
                </option>

            </select>

        </div>


        <div>

            <label
                for="notes"
                class="block text-xs font-semibold text-gray-400 mb-2"
            >
                Catatan
            </label>

            <textarea
                id="notes"
                name="notes"
                rows="3"
                placeholder="Tambahkan catatan retur jika diperlukan..."
                class="w-full
                       rounded-xl
                       bg-gray-900
                       border border-white/10
                       px-4 py-3
                       text-sm
                       text-white
                       placeholder-gray-600
                       outline-none
                       resize-none
                       focus:border-emerald-500
                       focus:ring-1
                       focus:ring-emerald-500"
            >{{ old('notes') }}</textarea>

        </div>

    </div>

    {{-- ERROR PROSES RETUR --}}
@if(session('error'))

    <div
        class="bg-red-900/20
               border border-red-500/20
               rounded-2xl
               p-4"
    >

        <p class="text-xs font-semibold text-red-400">
            Retur tidak dapat disimpan
        </p>

        <p class="text-[11px] text-red-300 mt-2">
            {{ session('error') }}
        </p>

    </div>

@endif

    {{-- ERROR VALIDASI --}}
    @if($errors->any())

        <div
            class="bg-red-900/20
                   border border-red-500/20
                   rounded-2xl
                   p-4"
        >

            <p class="text-xs font-semibold text-red-400">
                Terjadi kesalahan
            </p>

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
    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               p-5"
    >

        <button
    type="submit"
    class="w-full
           rounded-xl
           bg-emerald-600
           hover:bg-emerald-500
           text-white
           px-4 py-3
           text-sm
           font-semibold
           transition
           shadow-lg
           shadow-emerald-900/20"
>
    Simpan Retur Supplier
</button>

<p class="text-[10px] text-gray-500 text-center mt-3">
    Pastikan jumlah barang dan alasan retur sudah benar sebelum disimpan.
</p>

    </div>

</form>

</div>@endsection