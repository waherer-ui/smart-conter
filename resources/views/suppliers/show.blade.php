@extends('layouts.app')

@section('title', 'Detail Supplier')
@section('header', '🚚')
@section('header_tools')

<form
    id="supplierDateFilterForm"
    action="{{ route('supplier.show', $supplier->id) }}"
    method="GET"
    class="flex items-center gap-1.5 overflow-x-auto"
>

    {{-- CARI INVOICE --}}
    <input
        type="text"
        name="search"
        value="{{ $search ?? '' }}"
        placeholder="Cari invoice..."
        class="w-32 sm:w-40
               shrink-0
               bg-gray-800
               border border-white/10
               rounded-lg
               px-2.5 py-1.5
               text-[11px]
               text-white
               placeholder-gray-600
               focus:outline-none
               focus:border-emerald-500/50"
    >

    {{-- PERIODE --}}
    <select
        name="period"
        onchange="handleSupplierDateFilter(this.value)"
        class="shrink-0
               bg-gray-800
               border border-white/10
               rounded-lg
               px-2.5 py-1.5
               text-[11px]
               text-gray-300
               focus:outline-none
               focus:border-emerald-500/50"
    >

        <option
            value="today"
            {{ ($period ?? '') === 'today' ? 'selected' : '' }}
        >
            Hari ini
        </option>

        <option
            value="7days"
            {{ ($period ?? '') === '7days' ? 'selected' : '' }}
        >
            7 hari terakhir
        </option>

        <option
            value="month"
            {{ ($period ?? '') === 'month' ? 'selected' : '' }}
        >
            1 bulan terakhir
        </option>

        <option
            value="custom"
            {{ ($period ?? '') === 'custom' ? 'selected' : '' }}
        >
            Rentang tanggal
        </option>

    </select>

</form>


{{-- INPUT RENTANG TANGGAL --}}
<div
    id="supplierCustomDateInputs"
    class="{{ ($period ?? '') === 'custom' ? '' : 'hidden' }}
           mt-2"
>

    <form
        action="{{ route('supplier.show', $supplier->id) }}"
        method="GET"
        class="flex items-center gap-1.5 overflow-x-auto"
    >

        <input
            type="hidden"
            name="period"
            value="custom"
        >

        <input
            type="text"
            name="search"
            value="{{ $search ?? '' }}"
            placeholder="Invoice..."
            class="w-28
                   shrink-0
                   bg-gray-800
                   border border-white/10
                   rounded-lg
                   px-2 py-1.5
                   text-[11px]
                   text-white
                   placeholder-gray-600
                   focus:outline-none"
        >

        <input
            type="date"
            name="start_date"
            value="{{ $startDate ?? '' }}"
            class="shrink-0
                   bg-gray-800
                   border border-white/10
                   rounded-lg
                   px-2 py-1.5
                   text-[11px]
                   text-gray-300
                   focus:outline-none"
        >

        <span class="text-[10px] text-gray-600 shrink-0">
            sampai
        </span>

        <input
            type="date"
            name="end_date"
            value="{{ $endDate ?? '' }}"
            class="shrink-0
                   bg-gray-800
                   border border-white/10
                   rounded-lg
                   px-2 py-1.5
                   text-[11px]
                   text-gray-300
                   focus:outline-none"
        >

        <button
            type="submit"
            class="shrink-0
                   px-3 py-1.5
                   rounded-lg
                   bg-emerald-500
                   hover:bg-emerald-400
                   text-gray-950
                   text-[11px]
                   font-semibold"
        >
            Terapkan
        </button>

    </form>

</div>

@endsection

@section('content')

<div class="max-w-2xl mx-auto space-y-4">

    {{-- HEADER --}}
    <div>
        <h1 class="text-lg font-semibold text-white">
            Detail Supplier
        </h1>

        <p class="text-xs text-gray-500 mt-1">
            Informasi supplier toko Anda.
        </p>
    </div>


    {{-- PROFIL SUPPLIER --}}
    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               overflow-hidden"
    >

        <div class="p-5">

            {{-- ICON --}}
            <div class="flex items-center gap-3">

                <div
                    class="w-11 h-11
                           rounded-xl
                           bg-emerald-500/10
                           border border-emerald-500/20
                           flex items-center
                           justify-center
                           text-xl"
                >
                    🚚
                </div>

                <div class="min-w-0">

                    <h2
                        class="text-base
                               font-semibold
                               text-white
                               truncate"
                    >
                        {{ $supplier->name }}
                    </h2>

                    <p class="text-xs text-gray-500 mt-0.5">
                        Supplier toko
                    </p>

                </div>

            </div>


            {{-- INFORMASI --}}
            <div class="mt-5 space-y-3">

                @if($supplier->phone)

                    <div
                        class="flex items-start
                               gap-3
                               py-3
                               border-t
                               border-white/5"
                    >
                        <span class="text-sm">
                            📱
                        </span>

                        <div>
                            <p class="text-[11px] text-gray-500">
                                Nomor Telepon
                            </p>

                            <p class="text-sm text-gray-200 mt-0.5">
                                {{ $supplier->phone }}
                            </p>
                        </div>
                    </div>

                @endif


                @if($supplier->address)

                    <div
                        class="flex items-start
                               gap-3
                               py-3
                               border-t
                               border-white/5"
                    >
                        <span class="text-sm">
                            📍
                        </span>

                        <div>
                            <p class="text-[11px] text-gray-500">
                                Alamat
                            </p>

                            <p class="text-sm text-gray-200 mt-0.5">
                                {{ $supplier->address }}
                            </p>
                        </div>
                    </div>

                @endif


                @if($supplier->notes)

                    <div
                        class="flex items-start
                               gap-3
                               py-3
                               border-t
                               border-white/5"
                    >
                        <span class="text-sm">
                            📝
                        </span>

                        <div>
                            <p
                                class="text-[11px] text-gray-500"
                            >
                                Catatan
                            </p>

                            <p
                                class="text-sm
                                       text-gray-200
                                       mt-0.5
                                       whitespace-pre-line"
                            >
                                {{ $supplier->notes }}
                            </p>
                        </div>
                    </div>

                @endif

            </div>

        </div>


        {{-- ACTION --}}
        <div
            class="px-5 py-4
                   border-t border-white/10
                   flex items-center
                   justify-between gap-2"
        >

            <a
                href="{{ route('supplier.index') }}"
                class="px-4 py-2.5
                       rounded-xl
                       text-xs
                       font-semibold
                       text-gray-400
                       hover:text-white
                       hover:bg-white/5
                       transition"
            >
                ← Kembali
            </a>

            <a
                href="{{ route('supplier.edit', $supplier->id) }}"
                class="px-4 py-2.5
                       rounded-xl
                       text-xs
                       font-semibold
                       bg-emerald-500
                       hover:bg-emerald-400
                       text-gray-950
                       transition"
            >
                ✏️ Edit Supplier
            </a>

        </div>

    </div>


    {{-- =========================================================
         RIWAYAT PEMBELIAN
    ========================================================== --}}
    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               overflow-hidden"
    >

        {{-- HEADER RIWAYAT --}}
        <div
            class="px-4 py-4
                   border-b border-white/10
                   flex items-center
                   justify-between gap-3"
        >

            <div>
                <h2 class="text-sm font-semibold text-white">
                    📦 Riwayat Pembelian
                </h2>

                <p class="text-[11px] text-gray-500 mt-1">
                    Daftar pembelian dari supplier ini.
                </p>
            </div>

            <a
                href="{{ route('purchase.index') }}"
                class="inline-flex items-center gap-2
                       mt-4
                       px-4 py-2.5
                       rounded-xl
                       bg-white/5
                       hover:bg-white/10
                       border border-white/10
                       text-gray-300
                       text-xs
                       font-semibold
                       transition"
            >
                Lihat Semua Pembelian
            </a>

        </div>


        {{-- LIST PEMBELIAN --}}
        @forelse($purchases as $purchase)

            <a
                href="{{ route('purchase.show', $purchase->id) }}"
                class="block
                       px-4 py-4
                       border-b border-white/5
                       last:border-b-0
                       hover:bg-white/[0.03]
                       transition"
            >

                <div
                    class="flex items-center
                           justify-between
                           gap-3"
                >

                    {{-- INFO --}}
                    <div class="min-w-0">

                        <p
                            class="text-sm
                                   font-semibold
                                   text-white
                                   truncate"
                        >
                            {{ $purchase->invoice_number ?: 'Tanpa nomor invoice' }}
                        </p>

                        <p class="text-[11px] text-gray-500 mt-1">
                            📅 {{ $purchase->purchase_date->format('d/m/Y') }}
                        </p>

                        <p class="text-[11px] text-gray-500 mt-1">
                            {{ $purchase->items->sum('quantity') }} pcs
                            <span class="text-gray-700 mx-1">•</span>
                            {{ $purchase->items->count() }} jenis
                        </p>

                    </div>


                    {{-- TOTAL --}}
                    <div class="text-right shrink-0">

                        <p
                            class="text-sm
                                   font-semibold
                                   text-emerald-400"
                        >
                            Rp {{ number_format($purchase->total, 0, ',', '.') }}
                        </p>

                        <p class="text-[10px] text-gray-600 mt-1">
                            Lihat detail →
                        </p>

                    </div>

                </div>

            </a>

        @empty

            <div class="px-4 py-10 text-center">

                <div class="text-3xl mb-3">
                    📦
                </div>

                <h3 class="text-sm font-semibold text-white">
                    Belum ada pembelian
                </h3>

                <p class="text-[11px] text-gray-500 mt-2">
                    Riwayat pembelian dari supplier ini akan muncul di sini.
                </p>

                <a
                  href="{{ route('purchase.index') }}"
                  class="inline-flex items-center gap-2
                         mt-4
                         px-4 py-2.5
                         rounded-xl
                         bg-white/5
                         hover:bg-white/10
                         border border-white/10
                         text-gray-300
                         text-xs
                         font-semibold
                         transition"
              >
                  Lihat Semua Pembelian
              </a>

            </div>

        @endforelse

    </div>

</div>


<script>

function handleSupplierDateFilter(value) {

    const customInputs =
        document.getElementById(
            'supplierCustomDateInputs'
        );

    if (!customInputs) {
        return;
    }

    if (value === 'custom') {

        customInputs.classList.remove(
            'hidden'
        );

        return;
    }

    customInputs.classList.add(
        'hidden'
    );

    document
        .getElementById(
            'supplierDateFilterForm'
        )
        .submit();
}

</script>


@endsection