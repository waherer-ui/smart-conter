@extends('layouts.app')

@section('title', 'Bayar Utang')
@section('header', '💵')

@section('content')

<div class="max-w-xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-3 mb-5">

        <a
            href="{{ route('pelanggan.show', $debt->customer_id) }}"
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
                Bayar Utang
            </h2>

            <p class="text-xs text-gray-400">
                Catat pembayaran pelanggan
            </p>
        </div>

    </div>


    {{-- INFORMASI UTANG --}}
    <div class="bg-gray-800/80
                border border-white/10
                rounded-2xl
                p-4 mb-4">

        <div class="text-[11px] text-gray-500 uppercase">
            Pelanggan
        </div>

        <div class="text-sm font-semibold text-white mt-1">
            {{ $debt->customer->name }}
        </div>

        @if($debt->customer->phone)
            <div class="text-xs text-gray-400 mt-1">
                📱 {{ $debt->customer->phone }}
            </div>
        @endif

        @if($debt->description)
            <div class="text-xs text-gray-400 mt-3">
                {{ $debt->description }}
            </div>
        @endif

    </div>


    {{-- RINGKASAN UTANG --}}
    <div class="bg-gray-800/80
                border border-white/10
                rounded-2xl
                p-5 mb-4">

        <div class="grid grid-cols-2 gap-3">

            <div class="bg-gray-900/70
                        border border-white/5
                        rounded-xl
                        px-4 py-3">

                <div class="text-[11px] text-gray-500">
                    Total Utang
                </div>

                <div class="text-sm font-bold text-white mt-1">
                    Rp {{ number_format($debt->amount, 0, ',', '.') }}
                </div>

            </div>


            <div class="bg-gray-900/70
                        border border-white/5
                        rounded-xl
                        px-4 py-3">

                <div class="text-[11px] text-gray-500">
                    Sisa Utang
                </div>

                <div class="text-sm font-bold text-red-400 mt-1">
                    Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                </div>

            </div>

        </div>

    </div>


    {{-- FORM PEMBAYARAN --}}
    <div class="bg-gray-800/80
                border border-white/10
                rounded-2xl
                p-5">

        <form
            action="{{ route('pelanggan.debt.payment.store', $debt->id) }}"
            method="POST"
            class="space-y-4"
        >

            @csrf

            {{-- JUMLAH --}}
            <div>

                <label
                    class="block text-xs font-medium text-gray-300 mb-1.5"
                >
                    Jumlah Pembayaran
                </label>

                <input
                    type="number"
                    name="amount"
                    min="1"
                    max="{{ $debt->remaining_amount }}"
                    step="1"
                    inputmode="numeric"
                    value="{{ old('amount') }}"
                    required
                    placeholder="Contoh: 100000"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           placeholder-gray-500
                           focus:outline-none
                           focus:border-emerald-500"
                >

                @error('amount')
                    <p class="text-xs text-red-400 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- TANGGAL --}}
            <div>

                <label
                    class="block text-xs font-medium text-gray-300 mb-1.5"
                >
                    Tanggal Pembayaran
                </label>

                <input
                    type="date"
                    name="payment_date"
                    value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                    required
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           focus:outline-none
                           focus:border-emerald-500"
                >

                @error('payment_date')
                    <p class="text-xs text-red-400 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- CATATAN --}}
            <div>

                <label
                    class="block text-xs font-medium text-gray-300 mb-1.5"
                >
                    Catatan
                    <span class="text-gray-500">
                        (opsional)
                    </span>
                </label>

                <textarea
                    name="note"
                    rows="3"
                    maxlength="1000"
                    placeholder="Contoh: Cicilan pertama"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           placeholder-gray-500
                           focus:outline-none
                           focus:border-emerald-500
                           resize-none"
                >{{ old('note') }}</textarea>

                @error('note')
                    <p class="text-xs text-red-400 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- TOMBOL --}}
            <div class="flex gap-2 pt-2">

                <a
                    href="{{ route('pelanggan.show', $debt->customer_id) }}"
                    class="flex-1
                           text-center
                           bg-gray-700
                           hover:bg-gray-600
                           text-gray-200
                           px-4 py-3
                           rounded-xl
                           text-xs
                           font-semibold
                           transition"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="flex-1
                           bg-emerald-500
                           hover:bg-emerald-400
                           text-gray-950
                           px-4 py-3
                           rounded-xl
                           text-xs
                           font-semibold
                           transition"
                >
                    Simpan Pembayaran
                </button>

            </div>

        </form>

    </div>

</div>

@endsection